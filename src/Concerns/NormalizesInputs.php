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

            if (str_contains($input, '*')) {

                $this->applyWildcard(
                    prepared: $prepared,
                    input: $input,
                    config: $inputConfigs[$input] ?? [],
                );

                continue;
            }

            $value = $this->prepareValue(
                input: $input,
                config: $inputConfigs[$input] ?? [],
            );

            data_set(target: $prepared, key: $input, value: $value);
        }

        if (filled($prepared)) {

            $this->merge($prepared);
        }
    }

    /**
     * Run the full pipeline on a single input value
     */
    private function prepareValue(string $input, array $config): mixed
    {
        $value = $this->input($input);

        $value = $this->applyNormalize(value: $value, config: $config);

        $value = $this->applyNullIfBlank(value: $value, config: $config);

        $value = $this->applyPipes(value: $value, config: $config);

        return $value;
    }

    /**
     * Apply the pipeline to every item matched by a wildcard rule key at any nesting level
     */
    private function applyWildcard(array &$prepared, string $input, array $config): void
    {
        $segments = explode('.', $input);

        $firstStar = array_search('*', $segments);

        $parentKey = implode('.', array_slice($segments, 0, $firstStar));

        $parent = $this->input($parentKey, []);

        if (!is_array($parent)) {

            return;
        }

        $remainingSegments = array_slice($segments, $firstStar + 1);

        $parent = $this->walkWildcardItems(
            items: $parent,
            segments: $remainingSegments,
            config: $config,
        );

        data_set(target: $prepared, key: $parentKey, value: $parent);
    }

    /**
     * Recursively walk nested arrays at each wildcard level, applying the pipeline to leaf fields
     */
    private function walkWildcardItems(array $items, array $segments, array $config): array
    {
        if ($segments === []) {

            return $items;
        }

        $starIndex = array_search('*', $segments);

        if ($starIndex === false) {

            $fieldPath = implode('.', $segments);

            foreach ($items as $index => $item) {

                if (!is_array($item)) {

                    continue;
                }

                $fieldValue = data_get(target: $item, key: $fieldPath);

                $fieldValue = $this->applyNormalize(value: $fieldValue, config: $config);

                $fieldValue = $this->applyNullIfBlank(value: $fieldValue, config: $config);

                $fieldValue = $this->applyPipes(value: $fieldValue, config: $config);

                data_set(target: $item, key: $fieldPath, value: $fieldValue);

                $items[$index] = $item;
            }

            return $items;
        }

        $prefixKey = implode('.', array_slice($segments, 0, $starIndex));

        $suffixSegments = array_slice($segments, $starIndex + 1);

        foreach ($items as $index => $item) {

            if (!is_array($item)) {

                continue;
            }

            $subItems = $prefixKey !== '' ? data_get(target: $item, key: $prefixKey) : $item;

            if (!is_array($subItems)) {

                continue;
            }

            $subItems = $this->walkWildcardItems(
                items: $subItems,
                segments: $suffixSegments,
                config: $config,
            );

            if ($prefixKey !== '') {

                data_set(target: $item, key: $prefixKey, value: $subItems);
            }

            $items[$index] = $item;
        }

        return $items;
    }

    /**
     * Run normalizeInput on the value unless skipNormalize is set
     */
    private function applyNormalize(mixed $value, array $config): mixed
    {
        if ($config['skipNormalize'] ?? false) {

            return $value;
        }

        return normalizeInput(value: $value);
    }

    /**
     * Convert blank values to null, with optional fallback and zero handling
     */
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

    /**
     * Execute one or more custom pipe transformations on the value
     */
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
     * Dot notation and wildcards:
     *   Rule keys like user.name (dot notation) and user.*.name (wildcards) are
     *   supported. The config key must match the rule key exactly, including the
     *   wildcard pattern:
     *
     *     'user.name'      => ['default' => 'Guest']
     *     'user.*.name'    => ['skipNormalize' => true]
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
     *       'raw'          => ['skipNormalize' => true, 'keepBlank' => true],
     *       'user.*.name'  => ['default' => 'Guest'],
     *   ]
     *
     * Inputs not listed here still get both normalizeInput and nullIfBlank.
     */
    protected function inputConfigs(): array
    {
        return [];
    }
}
