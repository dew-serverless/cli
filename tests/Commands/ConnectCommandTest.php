<?php

declare(strict_types=1);

use Dew\Cli\ArrayManifest;
use Dew\Cli\Commands\ConnectCommand;
use Dew\Cli\Configuration\ArrayRepository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Http\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Mockery as m;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

test('unauthenticated user cannot connect', function (): void {
    $mock = m::mock(Client::class);
    $repository = new ArrayRepository;
    $manifest = new ArrayManifest;

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('You must be logged in to perform this action');
});

test('user cannot connect if project is not initialized', function (): void {
    $mock = m::mock(Client::class);
    $repository = new ArrayRepository(['token' => 'valid-token']);
    $manifest = new ArrayManifest;

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('Project is not initialized');
});

test('user can connect with valid credentials', function (): void {
    $repository = new ArrayRepository(['token' => 'valid-token']);
    $manifest = new ArrayManifest(['id' => 1]);

    $projectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'is_acs_connected' => false]]));

    $mock = m::mock(Client::class);
    $mock->shouldReceive('getProject')->once()->andReturn(new Response($projectResponse));
    $mock->shouldReceive('connectAcsAccount')->with(1, ['access_key_id' => 'foo', 'access_key_secret' => 'bar'])->andReturn(new Response(new Psr7Response(201)));

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $tester->setInputs(['foo', 'bar']);
    $tester->execute([]);

    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('Successfully connected Alibaba Cloud account');
});

test('user is informed of connection failure', function (): void {
    $repository = new ArrayRepository(['token' => 'valid-token']);
    $manifest = new ArrayManifest(['id' => 1]);

    $projectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'is_acs_connected' => false]]));
    $connectResponse = new Psr7Response(400, [], json_encode(['message' => 'Unknown error occurred.']));

    $mock = m::mock(Client::class);
    $mock->shouldReceive('getProject')->once()->andReturn(new Response($projectResponse));
    $mock->shouldReceive('connectAcsAccount')->with(1, ['access_key_id' => 'foo', 'access_key_secret' => 'bar'])->andReturn(new Response($connectResponse));

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $tester->setInputs(['foo', 'bar']);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('Could not connect to Alibaba Cloud: Unknown error occurred.');
});

test('user is informed when project is already connected', function (): void {
    $repository = new ArrayRepository(['token' => 'valid-token']);
    $manifest = new ArrayManifest(['id' => 1]);

    $projectResponse = new Psr7Response(200, [], json_encode(['data' => ['id' => 1, 'is_acs_connected' => true]]));

    $mock = m::mock(Client::class);
    $mock->shouldReceive('getProject')->once()->andReturn(new Response($projectResponse));

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('This project is already connected to Alibaba Cloud.');
});

test('user is informed when project retrieval fails', function (): void {
    $repository = new ArrayRepository(['token' => 'valid-token']);
    $manifest = new ArrayManifest(['id' => 1]);

    $projectResponse = new Psr7Response(500, [], json_encode(['message' => 'Server error']));

    $mock = m::mock(Client::class);
    $mock->shouldReceive('getProject')->once()->andReturn(new Response($projectResponse));

    $command = new ConnectCommand($mock, $repository, $manifest);
    $tester = new CommandTester($command);
    $exitCode = $tester->execute([]);

    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('Could not retrieve project: Server error');
});
