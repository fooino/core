<?php

namespace Fooino\Core\Actions;

use Fooino\Core\Models\Language;

class GetPaginatedLanguagesAction
{
    public function run(
        array|string $select = ['*'],
        array|string $with = [],
        array|string $withCount = []
    ) {
        return Language::select($select)
            ->search(request()->input('search'))
            ->direction(request()->input('direction'))
            ->status(request()->input('status'))
            ->with($with)
            ->withCount($withCount)
            ->paginate(pg())
            ->appends(request()->all());
    }
}
