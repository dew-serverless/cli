<?php

declare(strict_types=1);

use Dew\Cli\ArrayManifest;
use Dew\Cli\Commands\InitCommand;
use Dew\Cli\Configuration\ArrayRepository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Http\Response;
use Dew\Cli\Manifest;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Mockery as m;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

test('unauthenticated user cannot initialize project', function (): void {
    $mockedClient = m::mock(Client::class);
    $repository = new ArrayRepository;
    $manifest = new ArrayManifest;
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('You must be logged in to perform this action');
});

test('user cannot initialize if project is already initialized', function (): void {
    $manifest = new ArrayManifest(['id' => 1]);
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $mockedClient = m::mock(Client::class);
    $repository = new ArrayRepository(['token' => 'valid-token']);

    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('Project is already initialized');
});

test('user can create a new project', function (): void {
    $manifest = new ArrayManifest;
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $regionsResponse = new Psr7Response(200, [], json_encode(['data' => ['us-west-1', 'us-east-1']]));
    $slugCheckResponse = new Psr7Response(200, [], json_encode(['data' => ['available' => true]]));
    $createProjectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'name' => 'My Project', 'slug' => 'my-project', 'region' => 'us-west-1']]));

    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('regions')->andReturn(new Response($regionsResponse));
    $mockedClient->shouldReceive('checkProjectSlug')->with('my-project')->andReturn(new Response($slugCheckResponse));
    $mockedClient->shouldReceive('createProject')->andReturn(new Response($createProjectResponse));

    $repository = new ArrayRepository(['token' => 'valid-token']);
    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $tester->setInputs(['create', 'My Project', 'my-project', 'us-west-1', 'yes']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('Project successfully created!');

    expect($manifest->exists())->toBeTrue()
        ->and($manifest->get('id'))->toBe(1);
});

test('user can link to existing project', function (): void {
    $manifest = new ArrayManifest;
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $projectsResponse = new Psr7Response(200, [], json_encode(['data' => [
        ['id' => 1, 'name' => 'Project A', 'slug' => 'project-a', 'region' => 'us-west-1'],
        ['id' => 2, 'name' => 'Project B', 'slug' => 'project-b', 'region' => 'us-east-1'],
    ]]));

    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('listProjects')->andReturn(new Response($projectsResponse));

    $repository = new ArrayRepository(['token' => 'valid-token']);
    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $tester->setInputs(['link', 'project-a', 'yes']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('Project successfully linked!');

    expect($manifest->exists())->toBeTrue()
        ->and($manifest->get('id'))->toBe(1);
});

test('user can create project when no existing projects found', function (): void {
    $manifest = new ArrayManifest;
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $projectsResponse = new Psr7Response(200, [], json_encode(['data' => []]));
    $regionsResponse = new Psr7Response(200, [], json_encode(['data' => ['us-west-1']]));
    $slugCheckResponse = new Psr7Response(200, [], json_encode(['data' => ['available' => true]]));
    $createProjectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'name' => 'New Project', 'slug' => 'new-project', 'region' => 'us-west-1']]));

    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('listProjects')->andReturn(new Response($projectsResponse));
    $mockedClient->shouldReceive('regions')->andReturn(new Response($regionsResponse));
    $mockedClient->shouldReceive('checkProjectSlug')->with('new-project')->andReturn(new Response($slugCheckResponse));
    $mockedClient->shouldReceive('createProject')->andReturn(new Response($createProjectResponse));

    $repository = new ArrayRepository(['token' => 'valid-token']);
    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $tester->setInputs(['link', 'yes', 'New Project', 'new-project', 'us-west-1', 'yes']);
    $tester->execute([]);

    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('Project successfully created!');
});

test('user is informed when slug is already taken', function (): void {
    $manifest = new ArrayManifest;
    $rootPath = sys_get_temp_dir().'/'.uniqid('dew-cli-test-');

    $regionsResponse = new Psr7Response(200, [], json_encode(['data' => ['us-west-1']]));
    $slugCheckTakenResponse = new Psr7Response(200, [], json_encode(['data' => ['available' => false]]));
    $slugCheckAvailableResponse = new Psr7Response(200, [], json_encode(['data' => ['available' => true]]));
    $createProjectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'name' => 'My Project', 'slug' => 'my-project', 'region' => 'us-west-1']]));

    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('regions')->andReturn(new Response($regionsResponse));
    $mockedClient->shouldReceive('checkProjectSlug')->with('test-project')->andReturn(new Response($slugCheckTakenResponse));
    $mockedClient->shouldReceive('checkProjectSlug')->with('my-project')->andReturn(new Response($slugCheckAvailableResponse));
    $mockedClient->shouldReceive('createProject')->andReturn(new Response($createProjectResponse));

    $repository = new ArrayRepository(['token' => 'valid-token']);
    $command = new InitCommand($mockedClient, $repository, $manifest, $rootPath);
    $tester = new CommandTester($command);
    $tester->setInputs(['create', 'My Project', 'test-project', 'my-project', 'us-west-1', 'yes']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
});
