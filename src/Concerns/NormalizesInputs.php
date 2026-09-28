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
        $processed = [];

        foreach ($this->rules() as $input => $rules) {

            $config = $inputConfigs[$input] ?? [];

            $isWildcard = str_contains($input, '*');

            foreach ($this->resolveInputPaths(input: $input) as $path) {

                if ($isWildcard && isset($processed[$path])) {

                    // a field already prepared by an earlier rule wins over the wildcard pipeline
                    continue;
                }

                data_set(
                    target: $prepared,
                    key: $path,
                    value: $this->prepareValue(input: $path, config: $config),
                );

                $processed[$path] = true;
            }
        }

        if (filled($prepared)) {

            $this->mergePrepared(prepared: $prepared);
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
     * Resolve a rule key into the concrete input paths it covers, so wildcard and plain
     * keys can share the same pipeline
     */
    private function resolveInputPaths(string $input): array
    {
        return str_contains($input, '*')
            ? $this->expandWildcardPaths(pattern: $input)
            : [$input];
    }

    /**
     * Expand a wildcard rule key into every concrete leaf path it matches inside the current input
     */
    private function expandWildcardPaths(string $pattern): array
    {
        $segments = explode('.', $pattern);

        $firstStar = array_search('*', $segments);

        $parentKey = implode('.', array_slice($segments, 0, $firstStar));

        $parent = $this->input($parentKey, []);

        $remaining = array_slice($segments, $firstStar + 1);

        if (!is_array($parent) || $remaining === []) {

            return [];
        }

        $paths = [];

        $this->collectWildcardPaths(
            paths: $paths,
            value: $parent,
            segments: $remaining,
            path: $parentKey,
        );

        return $paths;
    }

    /**
     * Collect concrete leaf paths by matching segments against the data: a star iterates over
     * the items of the current level, a concrete segment descends into nested keys
     */
    private function collectWildcardPaths(array &$paths, array $value, array $segments, string $path): void
    {
        if ($segments === []) {

            return;
        }

        $starIndex = array_search('*', $segments);

        if ($starIndex === false) {

            $fieldPath = implode('.', $segments);

            foreach ($value as $index => $item) {

                if (is_array($item)) {

                    $paths[] = $path . '.' . $index . '.' . $fieldPath;
                }
            }

            return;
        }

        $prefixKey = implode('.', array_slice($segments, 0, $starIndex));

        $remaining = array_slice($segments, $starIndex + 1);

        foreach ($value as $index => $item) {

            if (!is_array($item)) {

                continue;
            }

            $subValue = $prefixKey !== '' ? data_get(target: $item, key: $prefixKey) : $item;

            if (!is_array($subValue)) {

                continue;
            }

            $this->collectWildcardPaths(
                paths: $paths,
                value: $subValue,
                segments: $remaining,
                path: $prefixKey !== '' ? $path . '.' . $index . '.' . $prefixKey : $path . '.' . $index,
            );
        }
    }

    /**
     * Merge the prepared values back into the request, deep-merging arrays so unruled
     * sibling keys inside the same parent are not lost
     */
    private function mergePrepared(array $prepared): void
    {
        $merged = [];

        foreach ($prepared as $key => $value) {

            if (is_array($value) && is_array($this->input($key))) {

                $value = array_replace_recursive($this->input($key), $value);
            }

            $merged[$key] = $value;
        }

        $this->merge($merged);
    }

    /**
     * Run normalizeInput on the value unless skipNormalize is set
     */
    protected function applyNormalize(mixed $value, array $config): mixed
    {
        if (($config['skipNormalize'] ?? false) === true) {

            return $value;
        }

        return normalizeInput(value: $value, includeHTMLTags: $config['includeHTMLTags'] ?? []);
    }

    /**
     * Convert blank values to null, with optional fallback and zero handling
     */
    protected function applyNullIfBlank(mixed $value, array $config): mixed
    {
        if (($config['keepBlank'] ?? false) === true) {

            return $value;
        }

        $default = $config['default'] ?? null;

        if (($config['nullOnZero'] ?? false) === true) {

            return nullIfBlankOrZero(value: $value, fallback: $default);
        }

        return nullIfBlank(value: $value, fallback: $default);
    }

    /**
     * Execute one or more custom pipe transformations on the value
     */
    protected function applyPipes(mixed $value, array $config): mixed
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
     *   normalizeInput → nullIfBlank or nullIfBlankOrZero → custom pipes
     *
     * Available config options:
     *
     *   skipNormalize: bool
     *       Skip normalizeInput for this input. Defaults to false.
     *
     *   includeHTMLTags: array
     *       HTML tags to keep when normalizing input. Defaults to []
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
     *   wildcard pattern. When a specific rule and a wildcard rule match the same
     *   field, the specific rule's value is kept.
     *
     *     'user.name'      => ['default' => 'Guest']
     *     'user.*.name'    => ['skipNormalize' => true]
     *
     * Example:
     *   [
     *       'title' => ['default' => 'Untitled', 'includeHTMLTags' => ['<custom>']],
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
