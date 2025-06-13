<?php
function animal_age(int $min, ?int $max, string $updated): string
{
    $monthsDelta = max(0, floor((time() - strtotime($updated)) / 2592000)); // ~30 днів

    $min = $min > 0 ? $min + $monthsDelta : 0;
    $max = $max !== null && $max > 0 ? $max + $monthsDelta : null;

    if ($min <= 0 && ($max === null || $max <= 0)) {
        return 'Невідомого віку';
    }

    $plural = function (int $num, string $one, string $few, string $many): string {
        $n = abs($num) % 100;
        $n1 = $n % 10;
        if ($n > 10 && $n < 20) return $many;
        if ($n1 > 1 && $n1 < 5) return $few;
        if ($n1 == 1) return $one;
        return $many;
    };

    $formatAge = function (int $m, bool $short = false) use ($plural): string {
        if ($m <= 0) return '';
        $y = intdiv($m, 12);
        $mo = $m % 12;

        if ($short) {
            if ($y > 0 && $mo === 0) {
                return "{$y}р";
            }
            if ($y === 0) {
                return "{$mo}м";
            }
            return "{$y}р {$mo}м";
        }

        if ($y > 0 && $mo === 0) {
            return "{$y} " . $plural($y, 'рік', 'роки', 'років');
        } elseif ($y === 0) {
            return "{$mo} " . $plural($mo, 'місяць', 'місяці', 'місяців');
        } else {
            return "{$y} " . $plural($y, 'рік', 'роки', 'років') . " {$mo} " . $plural($mo, 'місяць', 'місяці', 'місяців');
        }
    };

    if ($min > 0 && $max !== null && $max !== $min) {
        $onlyYears = $min % 12 === 0 && $max % 12 === 0;
        $onlyMonths = $min < 12 && $max < 12;
        $short = $onlyYears || $onlyMonths;

        return $formatAge($min, $short) . ' – ' . $formatAge($max, $short);
    }

    if ($min <= 0 && $max !== null) {
        return 'до ' . $formatAge($max);
    }

    return $formatAge($min) ?: 'Невідомого віку';
}
