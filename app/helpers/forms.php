<?php
if (!function_exists('form_error')) {
    function form_error(array $errs, string $field): string
    {
        if (empty($errs[$field])) return '';
        return '<div class="invalid-feedback d-block">'
            . htmlspecialchars($errs[$field][0]) . '</div>';
    }
}