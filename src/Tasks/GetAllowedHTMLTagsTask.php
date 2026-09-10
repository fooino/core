<?php

namespace Fooino\Core\Tasks;

use Fooino\Core\Support\SingletonableTask;

class GetAllowedHTMLTagsTask extends SingletonableTask
{
    /**
     * Return the HTML tags allowed to survive sanitization so rich text keeps its formatting while unsafe tags are stripped
     */
    protected function getData(): mixed
    {
        return [
            '<b>',
            '<strong>',
            '<em>',
            '<i>',
            '<u>',
            '<s>',
            '<sub>',
            '<sup>',
            '<p>',
            '<br>',
            '<hr>',
            '<pre>',
            '<code>',
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
            '<caption>',
            '<col>',
            '<colgroup>',
            '<td>',
            '<tr>',
            '<th>',
            '<thead>',
            '<tbody>',
            '<ul>',
            '<ol>',
            '<li>',
            '<dl>',
            '<dt>',
            '<dd>',
            '<blockquote>',
            '<q>',
            '<figure>',
            '<figcaption>',
            '<mark>',
            '<small>',
            '<del>',
            '<ins>',
            '<abbr>',
            '<cite>',
            '<a>',
            '<picture>'
        ];
    }
}
