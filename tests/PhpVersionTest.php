<?php

declare(strict_types=1);

use Dew\Cli\PhpVersion;

test('from composer returns null if could not find php package', function (string $path): void {
    expect(PhpVersion::fromComposer($path))->toBeNull();
})->with([
    __DIR__.'/Fixtures/Composer/empty.json',
    __DIR__.'/Fixtures/Composer/missing-php.json',
]);

test('from composer returns null if file does not exist', function (): void {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/404.json'))->toBeNull();
});

test('from composer returns the matched upper bound', function (): void {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/caret-80.json'))->toBe('8.4');
});

test('from composer returns null if could not find a matched version', function (): void {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/exact-99.json'))->toBeNull();
});

test('from runtime returns current php binary version', function (): void {
    expect(PhpVersion::fromRuntime())->not->toBeNull();
});
