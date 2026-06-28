<?php

namespace Fooino\Core\Concerns;

trait NormalizesInputs
{
    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        $inputConfigs = $this->inputConfigs();

        $prepared = [];

        foreach ($this->rules() as $input => $rules) {

            $prepared[$input] = $this->prepareValue(
                input: $input,
                config: $inputConfigs[$input] ?? [],
            );
        }

        if (filled($prepared)) {

            $this->merge($prepared);
        }
    }

    private function prepareValue(string $input, array $config): mixed
    {
        $value = $this->input($input);

        $value = $this->applyNormalize(value: $value, config: $config);

        $value = $this->applyNullIfBlank(value: $value, config: $config);

        $value = $this->applyPipes(value: $value, config: $config);

        return $value;
    }

    private function applyNormalize(mixed $value, array $config): mixed
    {
        if ($config['skipNormalize'] ?? false) {

            return $value;
        }

        return normalizeInput(value: $value);
    }

    private function applyNullIfBlank(mixed $value, array $config): mixed
    {
        if ($config['keepBlank'] ?? false) {

            return $value;
        }

        $default = $config['default'] ?? null;

        if ($config['nullOnZero'] ?? false) {

            return nullIfBlankOrZero(value: $value, fallback: $default);
        }

        return nullIfBlank(value: $value, fallback: $default);
    }

    private function applyPipes(mixed $value, array $config): mixed
    {
        $pipes = $config['pipe'] ?? null;

        if ($pipes === null) {

            return $value;
        }

        if (!is_array($pipes)) {

            $pipes = [$pipes];
        }

        foreach ($pipes as $pipe) {

            $value = $pipe($value, $this);
        }

        return $value;
    }

    /**
     * Define how each input should be prepared before validation.
     *
     * Return an array keyed by input name. Each value is a config array that
     * controls what transformations run on that input. The pipeline order is:
     *
     *   normalizeInput → nullIfBlank || nullIfBlankOrZero → custom pipes
     *
     * Available config options:
     *
     *   skipNormalize: bool
     *       Skip normalizeInput for this input. Defaults to false.
     *
     *   keepBlank: bool
     *       Keep blank values as-is instead of converting them to null.
     *       When true, nullIfBlank / nullIfBlankOrZero are skipped. Defaults to false.
     *
     *   nullOnZero: bool
     *       Use nullIfBlankOrZero instead of nullIfBlank. This also converts
     *       numeric zero (0, 0.0, '0') to null. Defaults to false.
     *
     *   default: mixed
     *       Fallback value returned when the input is blank or null.
     *       Works with both nullIfBlank and nullIfBlankOrZero.
     *
     *   pipe: callable|callable[]
     *       One or more transformations applied after nullIfBlank.
     *       Each callable receives ($value, $request) and returns the transformed value.
     *       Multiple pipes execute in sequence.
     *
     * Example:
     *   [
     *       'title' => ['default' => 'Untitled'],
     *       'slug'  => ['pipe' => fn($v, $request) => sanitizeSlug($v)],
     *       'count' => ['nullOnZero' => true, 'default' => 0],
     *       'phone' => ['pipe' => [
     *           fn($v, $request) => removeComma($v),
     *           fn($v, $request) => removeWhitespace($v),
     *           fn($v, $request) => sanitizeNumber($v),
     *       ]],
     *       'raw'   => ['skipNormalize' => true, 'keepBlank' => true],
     *   ]
     *
     * Inputs not listed here still get both normalizeInput and nullIfBlank.
     */
    abstract protected function inputConfigs(): array;
}
