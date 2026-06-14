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

    private string $field = 'token';

    private array $where = [];

    private string $format = 'numeric';

    private array $pipeline = [];

    private int $attempted = 0;

    private const int MAX_ATTEMPTED = 100;

    public const int MAX_LENGTH = 255;

    /**
     * To make and output token
     */
    public function token(): string
    {
        return $this
            ->validate()
            ->generate()
            ->token;
    }

    /**
     * Token setter
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

    public function model(string|Model $model): static
    {
        $this->model = ($model instanceof Model) ? get_class($model) : $model;

        return $this;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function field(string $field): static
    {
        $this->field = $field;

        return $this;
    }

    public function getField(): string
    {
        return $this->field;
    }

    public function where(array $where): static
    {
        $this->where = !is_array($where[0] ?? null) ? [$where] : $where;

        return $this;
    }

    public function getWhere(): array
    {
        return $this->where;
    }

    /**
     * Numeric format like 12345. Make OTP code 
     */
    public function numeric(): static
    {
        $this->format = 'numeric';

        return $this;
    }

    /**
     * Alpha Numeric format like 1A3f33xYo
     */
    public function alphaNumeric(): static
    {
        $this->format = 'alphaNumeric';

        return $this;
    }

    /**
     * Alphabet format like zAwfaqxYo
     */
    public function alphabet(): static
    {
        $this->format = 'alphabet';

        return $this;
    }

    public function weakPassword(): static
    {
        $this->format = 'weakPassword';

        return $this;
    }

    public function password(): static
    {
        $this->format = 'password';

        return $this;
    }

    public function strongPassword(): static
    {
        $this->format = 'strongPassword';

        return $this;
    }

    public function lowercase(): static
    {
        return $this->pipeline('strtolower');
    }

    public function uppercase(): static
    {
        return $this->pipeline('strtoupper');
    }

    protected function pipeline(string $method): static
    {
        $this->pipeline = array_merge($this->pipeline, [$method]);

        return $this;
    }

    protected function getPipeline(): array
    {
        return $this->pipeline;
    }

    /**
     * Format getter
     */
    public function getFormat(): string
    {
        return $this->format;
    }

    protected function validate(): static
    {
        if ($this->getLength() <= 0) {

            app(TokenGeneratorException::class)
                ->setMessage(FE['TOKEN_GENERATOR_LENGTH_MUST_BE_POSITIVE_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_LENGTH_MUST_BE_POSITIVE_CODE'])
                ->with($this->fooinoExceptionWith())
                ->throw();
        }

        if ($this->getLength() > self::MAX_LENGTH) {

            app(TokenGeneratorException::class)
                ->setMessage(FE['TOKEN_GENERATOR_BIG_LENGTH_NUMBER_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_BIG_LENGTH_NUMBER_CODE'])
                ->with($this->fooinoExceptionWith())
                ->throw();
        }

        if (
            $this->getFormat() === 'strongPassword' &&
            $this->getLength() < 12
        ) {

            app(TokenGeneratorException::class)
                ->setMessage(FE['TOKEN_GENERATOR_SMALL_LENGTH_NUMBER_FOR_STRONG_PASSWORD_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_SMALL_LENGTH_NUMBER_FOR_STRONG_PASSWORD_CODE'])
                ->with($this->fooinoExceptionWith())
                ->throw();
        }

        if (
            $this->getFormat() === 'password' &&
            $this->getLength() < 8
        ) {

            app(TokenGeneratorException::class)
                ->setMessage(FE['TOKEN_GENERATOR_SMALL_LENGTH_NUMBER_FOR_PASSWORD_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_SMALL_LENGTH_NUMBER_FOR_PASSWORD_CODE'])
                ->with($this->fooinoExceptionWith())
                ->throw();
        }

        if (
            filled($this->getModel()) &&
            blank($this->getField())
        ) {

            app(TokenGeneratorException::class)
                ->setMessage(FE['TOKEN_GENERATOR_FIELD_IS_REQUIRED_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_FIELD_IS_REQUIRED_CODE'])
                ->with($this->fooinoExceptionWith())
                ->throw();
        }

        return $this;
    }

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


    protected function generateNumeric(): string
    {
        $digits[] = random_int(1, 9); // the first digit must not be 0 to prevent unwanted problems

        for ($i = 0; $i < ($this->getLength() - 1); $i++) {

            $digits[] = random_int(0, 9);
        }

        return implode('', $digits);
    }

    protected function generateAlphaNumeric(): string
    {
        return $this->makeFromSet(set: array_merge(range(0, 9), range('a', 'z'), range('A', 'Z')));
    }

    protected function generateAlphabet(): string
    {
        return $this->makeFromSet(set: array_merge(range('a', 'z'), range('A', 'Z')));
    }

    protected function generateWeakPassword(): string
    {
        return str()->password(length: $this->getLength(), letters: false, numbers: true, symbols: false);
    }

    protected function generatePassword(): string
    {
        return str()->password(length: $this->getLength(), letters: true, numbers: true, symbols: false);
    }

    protected function generateStrongPassword(): string
    {
        return str()->password(length: $this->getLength(), letters: true, numbers: true, symbols: true);
    }

    protected function makeFromSet(array $set): string
    {
        $letters = [];
        shuffle($set);
        $max = count($set) - 1;

        for ($i = 0; $i < $this->getLength(); $i++) {
            $letters[] = $set[random_int(0, $max)];
        }

        return implode('', $letters);
    }

    protected function attempted(): void
    {
        $this->attempted++;

        if ($this->attempted > self::MAX_ATTEMPTED) {

            app(InfiniteLoopException::class)
                ->setMessage(FE['TOKEN_GENERATOR_MADE_INFINITE_LOOP_MESSAGE'])
                ->setCode(FE['TOKEN_GENERATOR_MADE_INFINITE_LOOP_CODE'])
                ->critical()
                ->shouldReport()
                ->with($this->fooinoExceptionWith())
                ->throw();
        }
    }

    private function fooinoExceptionWith(): array
    {
        return [
            'attempted' => $this->attempted,
            'length'    => $this->getLength(),
            'format'    => $this->getFormat()
        ];
    }
}
