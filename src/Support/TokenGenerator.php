<?php

namespace Fooino\Core\Support;

use Fooino\Core\Exceptions\InfiniteLoopException;
use Fooino\Core\Exceptions\TokenGeneratorException;

use Illuminate\Database\Eloquent\Model;

class TokenGenerator
{
    private string $token = '';

    private int $length = 5;

    private string $model = '';

    private string $field = '';

    private array $where = [];

    private string $format = 'numeric';

    private array $pipeline = [];

    private int $attempted = 0;

    private const int MAX_ATTEMPTED = 100;

    public const int MAX_LENGTH = 255;

    /**
     * Generate and return the token. This is the entry point that triggers validation,
     * generation, and optional uniqueness check against the database.
     */
    public function value(): string
    {
        return $this
            ->validate()
            ->generate()
            ->resetAttempted()
            ->token();
    }

    /**
     * Get the generated token.
     */
    protected function token(): string
    {
        return $this->token;
    }

    /**
     * Store the generated token internally.
     */
    protected function setToken(string $token): static
    {
        $this->token = $token;

        return $this;
    }

    /**
     * Length setter
     */
    public function length(int $length): static
    {
        $this->length = $length;

        return $this;
    }

    /**
     * Length getter
     */
    public function getLength(): int
    {
        return $this->length;
    }

    /**
     * Set the model class for database uniqueness check.
     * Accepts a fully qualified class name string or a Model instance.
     */
    public function model(Model|string $model): static
    {
        $this->model = ($model instanceof Model) ? get_class($model) : $model;

        return $this;
    }

    /**
     * Get the model class name used for uniqueness check.
     */
    public function getModel(): string
    {
        return $this->model;
    }

    /**
     * Set the database column name to check for token uniqueness.
     */
    public function field(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    /**
     * Get the database column name used for uniqueness check.
     */
    public function getField(): string
    {
        return $this->field;
    }

    /**
     * Set additional where conditions for the uniqueness database query.
     * Accepts a single flat array (e.g. ['col', '=', 'val']) or an array of arrays
     * (e.g. [['col1', '=', 'val1'], ['col2', '>', 'val2']]).
     */
    public function where(array $where): static
    {
        $this->where = $where === [] ? [] : (!is_array($where[0] ?? null) ? [$where] : $where);

        return $this;
    }

    /**
     * Get the where conditions used in the uniqueness database query.
     */
    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * Numeric format like 12345. Make OTP code
     */
    public function numeric(): static
    {
        return $this->setFormat('numeric');
    }

    /**
     * Alpha Numeric format like 1A3f33xYo
     */
    public function alphaNumeric(): static
    {
        return $this->setFormat('alphaNumeric');
    }

    /**
     * Alphabet format like zAwfaqxYo
     */
    public function alphabet(): static
    {
        return $this->setFormat('alphabet');
    }

    /**
     * Weak password format: only digits, no letters and no symbols.
     */
    public function weakPassword(): static
    {
        return $this->setFormat('weakPassword');
    }

    /**
     * Password format: letters and digits, no symbols.
     * Minimum length is 8 characters.
     */
    public function password(): static
    {
        return $this->setFormat('password');
    }

    /**
     * Strong password format: letters, digits, and symbols.
     * Minimum length is 12 characters.
     */
    public function strongPassword(): static
    {
        return $this->setFormat('strongPassword');
    }

    /**
     * UUID v4 format like 550e8400-e29b-41d4-a716-446655440000
     */
    public function uuid4(): static
    {
        return $this->setFormat('uuid4');
    }

    /**
     * UUID v7 format (time-ordered) like 018f3a6e-1b3c-7d45-a123-456789abcdef
     */
    public function uuid7(): static
    {
        return $this->setFormat('uuid7');
    }

    /**
     * Memorable OTP format: numeric token with at least one pair of adjacent identical
     * digits (e.g. 247719), making the code easier to remember for users.
     */
    public function memorableOtp(): static
    {
        return $this->setFormat('memorableOtp');
    }

    /**
     * Format setter
     */
    protected function setFormat(string $format): static
    {
        $this->format = $format;

        return $this;
    }

    /**
     * Format getter
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    /**
     * Apply strtolower transformation to the generated token via the pipeline.
     */
    public function lowercase(): static
    {
        return $this->pipeline('strtolower');
    }

    /**
     * Apply strtoupper transformation to the generated token via the pipeline.
     */
    public function uppercase(): static
    {
        return $this->pipeline('strtoupper');
    }

    /**
     * Append a transformation method to the pipeline.
     * Pipeline transformations are applied sequentially after token generation.
     */
    protected function pipeline(string $method): static
    {
        $this->pipeline = array_merge($this->getPipeline(), [$method]);

        return $this;
    }

    /**
     * Get the list of transformation methods queued in the pipeline.
     */
    public function getPipeline(): array
    {
        return $this->pipeline;
    }

    /**
     * Validate the current configuration before generation.
     * Checks length boundaries, format-specific minimums, and model/field consistency.
     */
    protected function validate(): static
    {
        if ($this->getLength() <= 0) {

            $this->throwLengthMustBePositiveException();
        }

        if ($this->getLength() > self::MAX_LENGTH) {

            $this->throwBigLengthNumberException();
        }

        if (
            $this->getFormat() === 'strongPassword' &&
            $this->getLength() < 12
        ) {

            $this->throwSmallLengthNumberForStrongPasswordException();
        }

        if (
            $this->getFormat() === 'password' &&
            $this->getLength() < 8
        ) {

            $this->throwSmallLengthNumberForPasswordException();
        }

        if (
            filled($this->getModel()) &&
            blank($this->getField())
        ) {

            $this->throwFieldIsRequiredException();
        }

        if (
            $this->getFormat() === 'memorableOtp' &&
            $this->getLength() < 2
        ) {

            $this->throwSmallLengthNumberForMemorableException();
        }

        return $this;
    }

    /**
     * Generate a token by calling the format-specific generator, applying pipeline
     * transformations, and recursively retrying if the token already exists in
     * the database (when a model is configured).
     */
    protected function generate(): static
    {
        $this->attempted();

        $token = $this->{('generate' . ucfirst($this->getFormat()))}();

        $token = $this->shape(token: $token);

        if (
            filled($this->getModel()) &&
            app($this->getModel())->where($this->getField(), $token)->where($this->getWhere())->exists()
        ) {

            return $this->generate();
        }

        return $this->setToken(token: $token);
    }

    /**
     * Apply all queued pipeline transformations (lowercase/uppercase) to the token sequentially.
     */
    protected function shape(string $token): string
    {
        foreach ($this->getPipeline() as $pipeline) {

            $token = match ($pipeline) {

                'strtolower'    => strtolower($token),

                'strtoupper'    => strtoupper($token)
            };
        }

        return $token;
    }

    /**
     * Generate a numeric-only token. The first digit is never 0 to avoid
     * issues with parsing the token as a number and losing leading zeros.
     */
    protected function generateNumeric(): string
    {
        $digits[] = random_int(1, 9); // the first digit must not be 0 to prevent unwanted problems

        for ($i = 1; $i < $this->getLength(); $i++) {

            $digits[] = random_int(0, 9);
        }

        return implode('', $digits);
    }

    /**
     * Generate an alphanumeric token using digits and both uppercase and lowercase letters.
     */
    protected function generateAlphaNumeric(): string
    {
        return $this->makeFromSet(set: array_merge(range(0, 9), range('a', 'z'), range('A', 'Z')));
    }

    /**
     * Generate a token using only alphabet letters (both uppercase and lowercase).
     */
    protected function generateAlphabet(): string
    {
        return $this->makeFromSet(set: array_merge(range('a', 'z'), range('A', 'Z')));
    }

    /**
     * Generate a weak password (digits only) using Laravel's string password helper.
     * Guarantees the first digit is never 0 to match numeric() behaviour.
     */
    protected function generateWeakPassword(): string
    {
        $token = str()->password(length: $this->getLength(), letters: false, numbers: true, symbols: false);

        if ($token[0] === '0') {
            $token[0] = (string) random_int(1, 9);
        }

        return $token;
    }

    /**
     * Generate a password (letters and digits) using Laravel's string password helper.
     */
    protected function generatePassword(): string
    {
        return str()->password(length: $this->getLength(), letters: true, numbers: true, symbols: false);
    }

    /**
     * Generate a strong password (letters, digits, and symbols) using Laravel's string password helper.
     */
    protected function generateStrongPassword(): string
    {
        return str()->password(length: $this->getLength(), letters: true, numbers: true, symbols: true);
    }

    /**
     * Generate a UUID v4 string using Laravel's string helper.
     */
    protected function generateUuid4(): string
    {
        return str()->uuid()->toString();
    }

    /**
     * Generate a UUID v7 string (time-ordered) using Laravel's string helper.
     */
    protected function generateUuid7(): string
    {
        return str()->uuid7()->toString();
    }

    /**
     * Generate a numeric token with at least one pair of adjacent identical digits.
     * Uses generateNumeric() internally and forces a duplicate if none exists.
     */
    protected function generateMemorableOtp(): string
    {
        $digits = str_split($this->generateNumeric());

        for ($i = 0; $i < $this->getLength() - 1; $i++) {

            if ($digits[$i] === $digits[$i + 1]) {

                return implode('', $digits);
            }
        }

        $pos = random_int(0, $this->getLength() - 2);

        $digits[$pos + 1] = $digits[$pos];

        return implode('', $digits);
    }

    /**
     * Build a token of the configured length by randomly picking characters from the given set.
     */
    protected function makeFromSet(array $set): string
    {
        shuffle($set);
        $letters = [];
        $max = count($set) - 1;

        for ($i = 0; $i < $this->getLength(); $i++) {

            $letters[] = $set[random_int(0, $max)];
        }

        return implode('', $letters);
    }

    /**
     * Reset the attempt counter after a successful generation cycle so that
     * reusing the same instance for another generation starts with a fresh budget.
     */
    protected function resetAttempted(): static
    {
        $this->attempted = 0;

        return $this;
    }

    /**
     * Increment the attempt counter and throw an InfiniteLoopException if
     * the maximum number of retries (100) is exceeded.
     */
    protected function attempted(): void
    {
        $this->attempted++;

        if ($this->attempted > self::MAX_ATTEMPTED) {

            $this->throwInfiniteLoopException();
        }
    }

    /**
     * Abort execution when the length is zero or negative
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1201
     */
    private function throwLengthMustBePositiveException(): never
    {
        app(TokenGeneratorException::class)
            ->_1201()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Abort execution when the length exceeds the maximum allowed value
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1202
     */
    private function throwBigLengthNumberException(): never
    {
        app(TokenGeneratorException::class)
            ->_1202()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Abort execution when the strongPassword format is used with insufficient length
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1203
     */
    private function throwSmallLengthNumberForStrongPasswordException(): never
    {
        app(TokenGeneratorException::class)
            ->_1203()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Abort execution when the password format is used with insufficient length
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1204
     */
    private function throwSmallLengthNumberForPasswordException(): never
    {
        app(TokenGeneratorException::class)
            ->_1204()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Abort execution when the model is configured but the field is empty
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1205
     */
    private function throwFieldIsRequiredException(): never
    {
        app(TokenGeneratorException::class)
            ->_1205()
            ->with(array_merge($this->fooinoExceptionWith(), ['field' => $this->getField()]))
            ->throw();
    }

    /**
     * Abort execution when the memorableOtp format is used with insufficient length
     *
     * @throws \Fooino\Core\Exceptions\TokenGeneratorException  with 1206
     */
    private function throwSmallLengthNumberForMemorableException(): never
    {
        app(TokenGeneratorException::class)
            ->_1206()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Abort execution when the uniqueness retry limit is exhausted
     *
     * @throws \Fooino\Core\Exceptions\InfiniteLoopException  with 253
     */
    private function throwInfiniteLoopException(): never
    {
        app(InfiniteLoopException::class)
            ->_253()
            ->with($this->fooinoExceptionWith())
            ->throw();
    }

    /**
     * Build the context array attached to thrown exceptions for debugging purposes.
     */
    private function fooinoExceptionWith(): array
    {
        return [
            'attempted' => $this->attempted,
            'length'    => $this->getLength(),
            'format'    => $this->getFormat()
        ];
    }
}
