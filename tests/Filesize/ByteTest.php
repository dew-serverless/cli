<?php

use Dew\Cli\Filesize\Byte;

test('size resolution', function () {
    expect(Byte::fromByte(1024)->size())->toBe(1024);
});

test('symbol resolution', function () {
    expect(Byte::fromByte(1024)->symbol())->toBe('B');
});
