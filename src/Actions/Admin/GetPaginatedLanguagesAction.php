<?php

namespace Fooino\Core\Actions\Admin;

use Fooino\Core\Models\Language;

class GetPaginatedLanguagesAction
{
    public function run(
        array|string $select = ['*'],
        array|string $with = [],
        array|string $withCount = []
    ) {
        return Language::select($select)
            ->search(ef('search'))
            ->direction(ef('direction'))
            ->status(ef('status'))
            ->with($with)
            ->withCount($withCount)
            ->paginate(pg())
            ->appends(request()->all());
    }
}
