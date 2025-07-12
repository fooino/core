<?php

namespace Fooino\Core\Tasks\Tools;

use Fooino\Core\Facades\Json;

class PrettifyInputTask
{
    public function run($key, $value): mixed
    {
        $isJson = Json::is(string: $value);
        if (
            $isJson
        ) {
            $value = Json::decodeToArray(json: $value);
        }

        if (
            \is_array($value)
        ) {
            \array_walk_recursive($value, fn(&$item, $itemKey) => $item = $this->replace(value: $item));
        }

        if (
            \is_string($value) ||
            \is_int($value) ||
            \is_float($value)
        ) {
            return $this->replace(value: $value);
        }

        return ($isJson) ? Json::encode($value) : $value;
    }


    private function replace(mixed $value): mixed
    {
        if (
            \is_null($value) ||
            \is_bool($value) ||
            \is_array($value) ||
            \is_object($value)
        ) {
            return $value;
        }

        $type = gettype($value);

        // Replace zero-width non-joiner character
        $value = preg_replace('/\x{200C}/u', '', $value);

        $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
        $arabic = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
        $english = range(0, 9);
        $replaced = str_replace($arabic, $english, str_replace($persian, $english, $value));

        $arabicLetters = ['ي', 'ك'];
        $persianLetters = ['ی', 'ک'];
        $replaced = str_replace($arabicLetters, $persianLetters, $replaced);

        $replaced = strip_tags($replaced, $this->allowedTags());

        $replaced = trimEmptyString(value: $replaced);

        settype($replaced, $type);
        return $replaced;
    }

    private function allowedTags(): array
    {
        return [
            '<b>',
            '<strong>',
            '<em>',
            '<i>',
            '<p>',
            '<br>',
            '<hr>',
            '<img>',
            '<button>',
            '<div>',
            '<span>',
            '<h1>',
            '<h2>',
            '<h3>',
            '<h4>',
            '<h5>',
            '<h6>',
            '<table>',
            '<td>',
            '<tr>',
            '<th>',
            '<thead>',
            '<tbody>',
            '<ul>',
            '<ol>',
            '<li>',
            '<a>',
            '<picture>'
        ];
    }
}
