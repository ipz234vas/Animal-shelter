<?php
if (!function_exists('render_permission_dropdown')) {
    function render_permission_dropdown(string $permString, int $userId): string
    {
        if ($permString === '') {
            return '<span class="text-muted small">Відсутні</span>';
        }

        $perms = classes\PermissionParser::fromString($permString);
        $btnId = "permDropdown-$userId";

        ob_start();
        ?>
        <div>
            <button class="btn btn-sm btn-light text-dark border rounded px-3 py-2" type="button"
                    id="<?= $btnId ?>" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-shield-lock"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="<?= $btnId ?>">
                <?php foreach ($perms as $perm): ?>
                    <li class="dropdown-item disabled"><?= htmlspecialchars($perm->label()) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
        <?php
        return ob_get_clean();
    }
}