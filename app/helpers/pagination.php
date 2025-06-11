<?php

use dto\pagination\PaginatedResult;

function paginationLinks(PaginatedResult $p, int $window = 2): string
{
    $last = $p->lastPage();
    $curr = $p->currentPage();

    if ($last === 1)
        return '';

    $curr = min($curr, $last);

    $makeUrl = function (int $page) {
        $params = $_GET;
        unset($params['page']);
        $params['page'] = $page;
        return '?' . http_build_query($params);
    };

    $html = '<nav><ul class="pagination">';

    $html .= sprintf(
        '<li class="page-item %s"><a class="page-link" href="%s">&laquo;</a></li>',
        $curr === 1 ? 'disabled' : '',
        $curr === 1 ? '#' : $makeUrl($curr - 1)
    );

    $start = max(1, $curr - $window);
    $end = min($last, $curr + $window);

    if ($start > 1) {
        $html .= "<li class='page-item'><a class='page-link' href='{$makeUrl(1)}'>1</a></li>";
        if ($start > 2) $html .= "<li class='page-item disabled'><span class='page-link'>…</span></li>";
    }

    for ($i = $start; $i <= $end; $i++) {
        $html .= sprintf(
            "<li class='page-item %s'><a class='page-link' href='%s'>%d</a></li>",
            $i === $curr ? 'active' : '',
            $makeUrl($i),
            $i
        );
    }

    if ($end < $last) {
        if ($end < $last - 1) $html .= "<li class='page-item disabled'><span class='page-link'>…</span></li>";
        $html .= "<li class='page-item'><a class='page-link' href='{$makeUrl($last)}'>$last</a></li>";
    }

    $html .= sprintf(
        '<li class="page-item %s"><a class="page-link" href="%s">&raquo;</a></li>',
        $curr === $last ? 'disabled' : '',
        $curr === $last ? '#' : $makeUrl($curr + 1)
    );

    return $html . '</ul></nav>';
}