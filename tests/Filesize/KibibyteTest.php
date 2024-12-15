<?php

use Dew\Cli\Filesize\Kibibyte;

test('size resolution', function () {
    expect(Kibibyte::fromByte(1024)->size())->toBe(1)
        ->and(Kibibyte::fromByte(1024 + 512)->size())->toBe(1.5);
});

test('symbol resolution', function () {
    expect(Kibibyte::fromByte(1024)->symbol())->toBe('KiB');
});
