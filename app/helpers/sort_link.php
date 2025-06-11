<?php

use dto\listRequests\UsersListRequest;

if (!function_exists('sort_link')) {
    function sort_link(string $label, string $field, UsersListRequest $req): string
    {
        $dir = ($req->sortBy === $field && $req->direction === 'ASC') ? 'DESC' : 'ASC';
        $icon = $req->sortBy === $field
            ? ($req->direction === 'ASC' ? 'bi bi-arrow-up' : 'bi bi-arrow-down')
            : 'bi bi-arrow-down-up';

        $params = $_GET;
        unset($params['page'], $params['sortBy'], $params['direction']);
        $params['sortBy'] = $field;
        $params['direction'] = $dir;
        $url = '?' . http_build_query($params);

        return sprintf('<a href="%s" class="text-decoration-none text-muted">%s <i class="%s ms-1"></i></a>',
            htmlspecialchars($url), htmlspecialchars($label), $icon);
    }
}