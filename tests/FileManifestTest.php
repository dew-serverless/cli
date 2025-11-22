<?php

declare(strict_types=1);

use Dew\Cli\FileManifest;

beforeEach(function (): void {
    $this->rootPath = sys_get_temp_dir().'/dew-cli-test-'.uniqid();
    $this->manifestPath = $this->rootPath.'/.dew/project.json';
    mkdir($this->rootPath, 0755, recursive: true);
});

afterEach(function (): void {
    if (is_file($this->rootPath.'/.dew/project.json')) {
        unlink($this->rootPath.'/.dew/project.json');
    }

    if (is_dir($this->rootPath.'/.dew')) {
        rmdir($this->rootPath.'/.dew');
    }

    if (is_dir($this->rootPath)) {
        rmdir($this->rootPath);
    }
});

test('file manifest does not exist when file is missing', function (): void {
    $manifest = new FileManifest($this->rootPath);
    expect($manifest->exists())->toBeFalse();
});

test('file manifest exists when file is present', function (): void {
    $manifest = new FileManifest($this->rootPath);
    $manifest->write(['id' => 1]);
    expect($manifest->exists())->toBeTrue();
});

test('file manifest can retrieve item from file', function (): void {
    $manifest = new FileManifest($this->rootPath);
    $manifest->write(['id' => 1, 'name' => 'test']);
    expect($manifest->get('id'))->toBe(1);
    expect($manifest->get('name'))->toBe('test');
});

test('file manifest returns default when key does not exist', function (): void {
    $manifest = new FileManifest($this->rootPath);
    $manifest->write(['id' => 1]);
    expect($manifest->get('missing'))->toBeNull();
    expect($manifest->get('missing', 'default'))->toBe('default');
});

test('file manifest throws exception when file does not exist', function (): void {
    $manifest = new FileManifest($this->rootPath);
    expect(fn (): array => $manifest->all())->toThrow(\RuntimeException::class);
});

test('file manifest can write data to file', function (): void {
    $manifest = new FileManifest($this->rootPath);
    $manifest->write(['id' => 1, 'name' => 'test']);
    expect($manifest->exists())->toBeTrue()
        ->and($manifest->get('id'))->toBe(1)
        ->and($manifest->get('name'))->toBe('test')
        ->and(file_exists($this->manifestPath))->toBeTrue();
});

test('file manifest writes with correct permissions', function (): void {
    $manifest = new FileManifest($this->rootPath);
    $manifest->write(['id' => 1]);
    expect(file_exists($this->manifestPath))->toBeTrue()
        ->and(substr(sprintf('%o', fileperms($this->manifestPath)), -4))->toBe('0644');
});

test('file manifest can retrieve all items from file', function (): void {
    $data = ['id' => 1, 'name' => 'test'];
    $manifest1 = new FileManifest($this->rootPath);
    $manifest1->write($data);
    expect($manifest1->all())->toBe($data);
    $manifest2 = new FileManifest($this->rootPath);
    expect($manifest2->all())->toBe($data);
});

test('file manifest refresh reloads data from file', function (): void {
    $manifest1 = new FileManifest($this->rootPath);
    $manifest1->write(['id' => 1]);

    $manifest2 = new FileManifest($this->rootPath);
    expect($manifest2->get('id'))->toBe(1);

    $manifest1->write(['id' => 2]);
    expect($manifest2->get('id'))->toBe(1);

    $manifest2->refresh();
    expect($manifest2->get('id'))->toBe(2);
});

test('file manifest handles empty json object', function (): void {
    mkdir(dirname($this->manifestPath), 0755, recursive: true);
    file_put_contents($this->manifestPath, '{}');
    $manifest = new FileManifest($this->rootPath);
    expect($manifest->exists())->toBeTrue()
        ->and($manifest->all())->toBe([]);
});

test('file manifest handles empty json array', function (): void {
    mkdir(dirname($this->manifestPath), 0755, recursive: true);
    file_put_contents($this->manifestPath, '[]');
    $manifest = new FileManifest($this->rootPath);
    expect($manifest->exists())->toBeTrue()
        ->and($manifest->all())->toBe([]);
});
