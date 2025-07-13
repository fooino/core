<?php

namespace Fooino\Core\Traits;

use Illuminate\Database\Eloquent\Builder;

trait FullTextSearch
{
    /**
     * Replaces spaces with full text search wildcards
     *
     * @param string $term
     * @return string
     */
    private function fullTextWildcards(mixed $term): string
    {
        if (!is_string($term) || blank($term)) return '';

        // Removing symbols used by MySQL and PostgreSQL
        $reservedSymbols = [
            '-',
            '+',
            '<',
            '>',
            '@',
            '(',
            ')',
            '~'
        ];
        $term = str_replace($reservedSymbols, ' ', $term);
        $term = trim($term);

        $words = explode(' ', $term);

        foreach ($words as $key => $word) {
            /*
             * Applying wildcards for only words greater than or equal to 3 characters,
             * since shorter words are not indexed by MySQL or PostgreSQL.
             */
            if (
                strlen($word) >= 3
            ) {

                if (
                    $this->isMysql()
                ) {
                    $words[$key] = '*' . $word . '*';
                }

                if (
                    $this->isPostgres()
                ) {
                    $words[$key] = "'$word:*'";
                }
            }
        }

        $searchTerm = $term;

        if (
            $this->isMysql()
        ) {
            $searchTerm = implode(' ', $words);
        }

        if (
            $this->isPostgres()
        ) {
            $searchTerm = implode(' | ', $words);
        }

        return $searchTerm;
    }

    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|float|bool|string|null $search
     */
    public function scopeFullTextSearch(Builder $query, int|float|bool|string|null $search = null): void
    {
        $columns = $this->getSearchable();
        $search = $this->fullTextWildcards(term: $search);

        if (
            $this->dbSupportFullText() &&
            filled($search) &&
            filled($columns)
        ) {
            if (
                $this->isMysql()
            ) {

                $query
                    ->selectRaw("*, MATCH ({$columns}) AGAINST (? IN NATURAL LANGUAGE MODE) AS SEARCH_SCORE", [$search])
                    ->whereRaw("MATCH ({$columns}) AGAINST (? IN NATURAL LANGUAGE MODE)", $search)
                    ->orderBy('SEARCH_SCORE', 'DESC');

                // 
            }

            if (
                $this->isPostgres()
            ) {

                $query
                    ->selectRaw("*, ts_rank(to_tsvector('simple', {$columns}), to_tsquery('simple', ?)) AS SEARCH_SCORE", [$search])
                    ->whereRaw("to_tsvector('simple', {$columns}) @@ to_tsquery('simple', ?)", [$search])
                    ->orderByRaw("2 DESC");
            }
        }
    }

    /**
     * Scope a query that matches a full text search of term.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param int|float|bool|string|null $search
     */
    public function scopeOrFullTextSearch(Builder $query, int|float|bool|string|null $search = null): void
    {
        $columns = $this->getSearchable();
        $search = $this->fullTextWildcards(term: $search);

        if (
            $this->dbSupportFullText() &&
            filled($search) &&
            filled($columns)
        ) {
            if (
                $this->isMysql()
            ) {

                $query->orWhereRaw("MATCH ({$columns}) AGAINST (? IN NATURAL LANGUAGE MODE)", $search);

                // 
            }

            if (
                $this->isPostgres()
            ) {

                $query->orWhereRaw("to_tsvector('simple', {$columns}) @@ to_tsquery('simple', ?)", [$search]);

                // 
            }
        }
    }

    private function getSearchable(): string
    {
        return implode(',', $this?->searchable ?? []);
    }

    private function dbSupportFullText(): bool
    {
        return !in_array(config('database.default'), ['sqlite', 'sqlite3']);
    }

    private function isMysql(): bool
    {
        return config('database.default') === 'mysql';
    }

    private function isPostgres(): bool
    {
        return config('database.default') === 'pgsql';
    }
}
