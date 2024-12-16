<?php

declare(strict_types=1);

use Dew\Cli\Filesize\Kibibyte;

test('size resolution', function (): void {
    expect(Kibibyte::fromByte(1024)->size())->toBe(1)
        ->and(Kibibyte::fromByte(1024 + 512)->size())->toBe(1.5);
});

test('symbol resolution', function (): void {
    expect(Kibibyte::fromByte(1024)->symbol())->toBe('KiB');
});
