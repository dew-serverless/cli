<?php

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Database\CreateDatabaseInstanceHandler;
use Dew\Cli\Tests\FakeStyle;
use Dew\Cli\Tests\Fixtures\StubMySqlDatabaseInstanceQuoter;
use Dew\Cli\Tests\Fixtures\StubMySqlServerlessDatabaseInstanceQuoter;
use Symfony\Component\Console\Input\InputInterface;

test('pay-as-you-go database instance creation', function () {
    $input = Mockery::spy(InputInterface::class);

    $io = (new FakeStyle)
        ->expectsOutput('Create a new database instance')
        ->expectsQuestion('What kind of instance do you want to setup', 'pay-as-you-go')
        ->expectsQuestion('What is your database instance name', 'datastore')
        ->expectsQuestion('What database engine do you want to setup', 'mysql')
        ->expectsQuestion('How about the database engine version', '8.0')
        ->expectsQuestion('What deployment option do you want to choose', 'Basic')
        ->expectsQuestion('What storage type do you want to choose', 'cloud_essd')
        ->expectsQuestion('Which zone the database instance is deployed to', 'us-west-1a')
        ->expectsQuestion('What instance class do you want to setup', 'mysql.n2.medium.1')
        ->expectsQuestion('How much storage in GB do you want to setup', '20')
        ->expectsTable([
            'Currency', 'Original', 'Discount', 'Trade',
        ], [
            ['CNY', 0.49, 0, 0.49],
        ])
        ->expectsOutput('Billed hourly.')
        ->expectsConfirmation('Confirm to create the database instance', false);

    (new CreateDatabaseInstanceHandler($input, $io))
        ->clientUsing(Mockery::mock(CommunicatesWithDew::class))
        ->forProject(9999)
        ->quoterUsing(StubMySqlDatabaseInstanceQuoter::makePayAsYouGo())
        ->handle();
});

test('subscription database instance creation', function () {
    $input = Mockery::spy(InputInterface::class);

    $io = (new FakeStyle)
        ->expectsOutput('Create a new database instance')
        ->expectsQuestion('What kind of instance do you want to setup', 'subscription')
        ->expectsQuestion('What is your database instance name', 'datastore')
        ->expectsQuestion('What database engine do you want to setup', 'mysql')
        ->expectsQuestion('How about the database engine version', '8.0')
        ->expectsQuestion('What deployment option do you want to choose', 'Basic')
        ->expectsQuestion('What storage type do you want to choose', 'cloud_essd')
        ->expectsQuestion('Which zone the database instance is deployed to', 'us-west-1a')
        ->expectsQuestion('What instance class do you want to setup', 'mysql.n2.medium.1')
        ->expectsQuestion('How much storage in GB do you want to setup', '20')
        ->expectsQuestion('What subscription type is feel right to you', 'month')
        ->expectsQuestion('How many months do you want to subscribe the instance', '1')
        ->expectsTable([
            'Currency', 'Original', 'Discount', 'Trade',
        ], [
            ['CNY', 0.49, 0, 0.49],
        ])
        ->expectsOutput('Subscription term: 1 month')
        ->expectsConfirmation('Confirm to create the database instance', false);

    (new CreateDatabaseInstanceHandler($input, $io))
        ->clientUsing(Mockery::mock(CommunicatesWithDew::class))
        ->forProject(9999)
        ->quoterUsing(StubMySqlDatabaseInstanceQuoter::makeSubscription())
        ->handle();
});

test('serverless database instance creation', function () {
    $input = Mockery::spy(InputInterface::class);

    $io = (new FakeStyle)
        ->expectsOutput('Create a new database instance')
        ->expectsQuestion('What kind of instance do you want to setup', 'serverless')
        ->expectsQuestion('What is your database instance name', 'datastore')
        ->expectsQuestion('What database engine do you want to setup', 'mysql')
        ->expectsQuestion('How about the database engine version', '8.0')
        ->expectsQuestion('What deployment option do you want to choose', 'serverless_basic')
        ->expectsQuestion('What storage type do you want to choose', 'cloud_essd')
        ->expectsQuestion('Which zone the database instance is deployed to', 'us-west-1a')
        ->expectsQuestion('What instance class do you want to setup', 'mysql.n2.serverless.1c')
        ->expectsQuestion('How much storage in GB do you want to setup', '20')
        ->expectsQuestion('What is the minimum RCU for instance scaling down', '0.5')
        ->expectsQuestion('What is the maximum RCU for instance scaling up', '2')
        ->expectsConfirmation('Enable auto-pause feature', false)
        ->expectsConfirmation('Enable force scaling', false)
        ->expectsTable([
            'Currency', 'From', 'To',
        ], [
            ['CNY', 0.19, 0.39],
        ])
        ->expectsOutput('Billed based on RCU, hourly')
        ->expectsConfirmation('Confirm to create the database instance', false);

    (new CreateDatabaseInstanceHandler($input, $io))
        ->clientUsing(Mockery::mock(CommunicatesWithDew::class))
        ->forProject(9999)
        ->quoterUsing(StubMySqlServerlessDatabaseInstanceQuoter::make())
        ->handle();
});

test('unsupported database instance creation', function () {
    $input = Mockery::spy(InputInterface::class);

    $io = (new FakeStyle)
        ->expectsOutput('Create a new database instance')
        ->expectsQuestion('What kind of instance do you want to setup', 'free');

    $handler = (new CreateDatabaseInstanceHandler($input, $io))
        ->clientUsing(Mockery::mock(CommunicatesWithDew::class))
        ->forProject(9999)
        ->quoterUsing(StubMySqlDatabaseInstanceQuoter::makePayAsYouGo());

    expect(fn () => $handler->handle())
        ->toThrow(InvalidArgumentException::class, 'Unsupported database instance type.');
});
