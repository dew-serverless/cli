<?php

use Dew\Cli\Filesize\Byte;
use Dew\Cli\Filesize\Filesize;
use Dew\Cli\Filesize\Kibibyte;
use Dew\Cli\Filesize\Mebibyte;

test('make byte resolution', function () {
    expect(Filesize::make(0))->toBeInstanceOf(Byte::class);
    expect(Filesize::make(512))->toBeInstanceOf(Byte::class);
    expect(Filesize::make(1024 - 1))->toBeInstanceOf(Byte::class);
});

test('make kibibyte resolution', function () {
    expect(Filesize::make(1024))->toBeInstanceOf(Kibibyte::class);
    expect(Filesize::make(1024 ** 2 - 1))->toBeInstanceOf(Kibibyte::class);
});

test('make mebibyte resolution', function () {
    expect(Filesize::make(1024 ** 2))->toBeInstanceOf(Mebibyte::class);
    expect(Filesize::make(1024 ** 3))->toBeInstanceOf(Mebibyte::class);
});

test('round rounds a filesize', function () {
    expect(Filesize::round(4))->toBe(4.0)
        ->and(Filesize::round(5))->toBe(5.0)
        ->and(Filesize::round(4.4))->toBe(4.0)
        ->and(Filesize::round(4.5))->toBe(5.0)
        ->and(Filesize::round(4.4, 1))->toBe(4.4)
        ->and(Filesize::round(4.44, 1))->toBe(4.4)
        ->and(Filesize::round(4.45, 1))->toBe(4.5)
        ->and(Filesize::round(4.95, 1))->toBe(5.0);
});
