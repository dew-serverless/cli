<?php

declare(strict_types=1);

use Dew\Cli\Commands\LoginCommand;
use Dew\Cli\Configuration\ArrayRepository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Http\Response;
use GuzzleHttp\Psr7\Response as Psr7Response;
use Mockery as m;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

test('user can authenticate with valid token', function (): void {
    $mockedResponse = new Psr7Response(200, [], json_encode(['id' => 1, 'name' => 'Zhineng', 'email' => 'im@zhineng.li']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('valid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $command = new LoginCommand($mockedClient, new ArrayRepository);
    $tester = new CommandTester($command);
    $tester->setInputs(['valid-token']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('You are logged in as Zhineng.');
});

test('token is stored after successful authentication', function (): void {
    $mockedResponse = new Psr7Response(200, [], json_encode(['id' => 1, 'name' => 'Zhineng', 'email' => 'im@zhineng.li']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('valid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $repository = new ArrayRepository;
    $command = new LoginCommand($mockedClient, $repository);
    $tester = new CommandTester($command);
    $tester->setInputs(['valid-token']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
    expect($repository->get('token'))->toBe('valid-token');
});

test('user cannot authenticate with invalid token', function (): void {
    $mockedResponse = new Psr7Response(401, [], json_encode(['message' => 'Unauthenticated.']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('invalid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $command = new LoginCommand($mockedClient, new ArrayRepository);
    $tester = new CommandTester($command);
    $tester->setInputs(['invalid-token']);
    $exitCode = $tester->execute([]);
    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('The token is invalid.');
});

test('invalid token should not be persisted', function (): void {
    $mockedResponse = new Psr7Response(401, [], json_encode(['message' => 'Unauthenticated.']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('invalid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $repository = new ArrayRepository;
    $command = new LoginCommand($mockedClient, $repository);
    $tester = new CommandTester($command);
    $tester->setInputs(['invalid-token']);
    $exitCode = $tester->execute([]);
    expect($exitCode)->toBe(Command::FAILURE);
    expect($repository->has('token'))->toBeFalse();
});

test('user is informed of error during authentication', function (): void {
    $mockedResponse = new Psr7Response(500, [], json_encode(['message' => 'Internal Server Error.']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('valid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $command = new LoginCommand($mockedClient, new ArrayRepository);
    $tester = new CommandTester($command);
    $tester->setInputs(['valid-token']);
    $exitCode = $tester->execute([]);
    expect($exitCode)->toBe(Command::FAILURE);
    expect($tester->getDisplay())->toContain('Failed to authenticate: Internal Server Error.');
});

test('user needs to confirm if token is already configured', function (): void {
    $mockedClient = m::mock(Client::class);
    $command = new LoginCommand($mockedClient, new ArrayRepository(['token' => 'existing-token']));
    $tester = new CommandTester($command);
    $tester->setInputs(['no']);
    $exitCode = $tester->execute([]);
    expect($exitCode)->toBe(Command::SUCCESS);
    expect($tester->getDisplay())->toContain('The API token has already been configured.');
});

test('user can overwrite existing token after confirmation', function (): void {
    $mockedResponse = new Psr7Response(200, [], json_encode(['id' => 1, 'name' => 'Zhineng', 'email' => 'im@zhineng.li']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('new-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $command = new LoginCommand($mockedClient, new ArrayRepository(['token' => 'existing-token']));
    $tester = new CommandTester($command);
    $tester->setInputs(['yes', 'new-token']);
    $tester->execute([]);
    $tester->assertCommandIsSuccessful();
    expect($tester->getDisplay())->toContain('You are logged in as Zhineng.');
});

test('invalid token during overwrite does not change existing token', function (): void {
    $mockedResponse = new Psr7Response(401, [], json_encode(['message' => 'Unauthenticated.']));
    $mockedClient = m::mock(Client::class);
    $mockedClient->shouldReceive('setToken')->with('invalid-token')->once();
    $mockedClient->shouldReceive('user')->andReturn(new Response($mockedResponse));
    $repository = new ArrayRepository(['token' => 'existing-token']);
    $command = new LoginCommand($mockedClient, $repository);
    $tester = new CommandTester($command);
    $tester->setInputs(['yes', 'invalid-token']);
    $exitCode = $tester->execute([]);
    expect($exitCode)->toBe(Command::FAILURE);
    expect($repository->get('token'))->toBe('existing-token');
});
