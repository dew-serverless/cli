<?php

declare(strict_types=1);

use Dew\Cli\ArrayManifest;

test('array manifest exists when data is not empty', function (): void {
    $manifest = new ArrayManifest(['id' => 1]);
    expect($manifest->exists())->toBeTrue();
});

test('array manifest does not exist when data is empty', function (): void {
    $manifest = new ArrayManifest;
    expect($manifest->exists())->toBeFalse();
});

test('array manifest can retrieve item', function (): void {
    $manifest = new ArrayManifest(['id' => 1, 'name' => 'test']);
    expect($manifest->get('id'))->toBe(1);
    expect($manifest->get('name'))->toBe('test');
});

test('array manifest returns default when key does not exist', function (): void {
    $manifest = new ArrayManifest(['id' => 1]);
    expect($manifest->get('missing'))->toBeNull();
    expect($manifest->get('missing', 'default'))->toBe('default');
});

test('array manifest can write data', function (): void {
    $manifest = new ArrayManifest;
    $manifest->write(['id' => 1, 'name' => 'test']);
    expect($manifest->exists())->toBeTrue();
    expect($manifest->get('id'))->toBe(1);
    expect($manifest->get('name'))->toBe('test');
});

test('array manifest can retrieve all items', function (): void {
    $data = ['id' => 1, 'name' => 'test'];
    $manifest = new ArrayManifest($data);
    expect($manifest->all())->toBe($data);
});

test('array manifest returns empty array when no data', function (): void {
    $manifest = new ArrayManifest;
    expect($manifest->all())->toBe([]);
});
