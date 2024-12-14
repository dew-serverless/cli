<?php

declare(strict_types=1);

use Dew\Cli\PhpVersion;

test('from composer returns false if could not find php package', function (string $path) {
    expect(PhpVersion::fromComposer($path))->toBeFalse();
})->with([
    __DIR__.'/Fixtures/Composer/empty.json',
    __DIR__.'/Fixtures/Composer/missing-php.json',
]);

test('from composer returns false if file does not exist', function () {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/404.json'))->toBeFalse();
});

test('from composer returns the matched upper bound', function () {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/caret-80.json'))->toBe('8.4');
});

test('from composer returns false if could not find a matched version', function () {
    expect(PhpVersion::fromComposer(__DIR__.'/Fixtures/Composer/exact-99.json'))->toBeFalse();
});
