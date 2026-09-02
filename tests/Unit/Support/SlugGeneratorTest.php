<?php

declare(strict_types=1);

use App\Support\SlugGenerator;

function slug(string $value): string
{
    return (new SlugGenerator)->generate($value);
}

it('lowercases and dasherizes a plain name', function (): void {
    expect(slug('Acme Widgets'))->toBe('acme-widgets');
});

it('collapses punctuation and repeated separators', function (): void {
    expect(slug('Acme,   Inc.'))->toBe('acme-inc');
});

it('trims leading and trailing separators', function (): void {
    expect(slug('  Hello World  '))->toBe('hello-world');
});

it('keeps digits', function (): void {
    expect(slug('Studio 54'))->toBe('studio-54');
});

it('returns an empty string for punctuation only input', function (): void {
    expect(slug('!!!'))->toBe('');
});
