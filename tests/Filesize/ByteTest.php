<?php

declare(strict_types=1);

use Dew\Cli\Filesize\Byte;

test('size resolution', function (): void {
    expect(Byte::fromByte(1024)->size())->toBe(1024);
});

test('symbol resolution', function (): void {
    expect(Byte::fromByte(1024)->symbol())->toBe('B');
});
