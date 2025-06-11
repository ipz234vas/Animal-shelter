<?php

namespace app\helpers;

use classes\ModelState;

final class FileValidator
{
    private const array IMG_MIME = ['image/jpeg', 'image/png'];
    private const array VID_MIME = ['video/mp4', 'video/webm'];
    private const int|float IMG_MAX = 5 * 1024 * 1024;   // 5 МБ
    private const int|float VID_MAX = 16 * 1024 * 1024;  // 20 МБ

    public static function tryStore(
        ?array     $file,
        string     $destDir,
        array      $mimes,
        int        $max,
        string     $statePath,
        ModelState $state
    ): ?string
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] === UPLOAD_ERR_INI_SIZE) {
            $state->add($statePath, 'Файл перевищує максимальний розмір.');
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $state->add($statePath, 'Помилка завантаження файлу.');
            return null;
        }
        $mime = mime_content_type($file['tmp_name']);
        if (!in_array($mime, $mimes, true)) {
            $state->add($statePath, 'Непідтримуваний тип файлу.');
            return null;
        }
        if ($file['size'] > $max) {
            $state->add($statePath, "Файл перевищує максимальний розмір у {$max}.");
            return null;
        }

        if (!is_dir($destDir) && !mkdir($destDir, 0777, true)) {
            $state->add($statePath, 'Не можу створити директорію для файлів.');
            return null;
        }

        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = uniqid('', true) . ".$ext";
        if (!move_uploaded_file($file['tmp_name'], "$destDir/$name")) {
            $state->add($statePath, 'Не вдалося зберегти файл.');
            return null;
        }
        return $name;
    }

    public static function saveImage(?array $f, ModelState $s, string $path = 'cover_image'): ?string
    {
        return self::tryStore($f, __DIR__ . '/../../uploads/images',
            self::IMG_MIME, self::IMG_MAX, $path, $s);
    }

    public static function saveVideo(?array $f, ModelState $s, string $path = 'intro_video'): ?string
    {
        return self::tryStore($f, __DIR__ . '/../../uploads/videos',
            self::VID_MIME, self::VID_MAX, $path, $s);
    }
}