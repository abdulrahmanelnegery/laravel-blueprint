<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Turns a display name into a URL safe slug: lower case, runs of
 * non alphanumeric characters collapsed to a single hyphen, no leading
 * or trailing hyphen. Punctuation only input yields an empty string.
 */
final class SlugGenerator
{
    public function generate(string $value): string
    {
        $hyphenated = preg_replace('/[^\p{L}\p{N}]+/u', '-', $value) ?? '';

        return trim(mb_strtolower($hyphenated), '-');
    }
}
