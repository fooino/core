<?php

namespace Fooino\Core\Tasks\Tag;

use Fooino\Core\Models\Tag;

class AddNewTagTask
{
    public function run(array $tags): void
    {
        if (
            filled($tags)
        ) {

            $insert = [];
            $dbTags = Tag::whereIn('name', $tags)->pluck('name')->toArray();
            $differences = \array_diff($tags, $dbTags);

            foreach ($differences as $difference) {
                $insert[] = [
                    'name'          => $difference,
                    'created_at'    => currentDate(),
                    'updated_at'    => currentDate(),
                ];
            }

            if (
                filled($insert)
            ) {
                Tag::insert($insert);
            }
        }
    }
}
