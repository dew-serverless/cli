<?php

use Dew\Cli\Filesize\Mebibyte;

test('size resolution', function () {
    expect(Mebibyte::fromByte(1024 ** 2)->size())->toBe(1)
        ->and(Mebibyte::fromByte(1024 * 512)->size())->toBe(0.5);
});

test('symbol resolution', function () {
    expect(Mebibyte::fromByte(1024 ** 2)->symbol())->toBe('MiB');
});
