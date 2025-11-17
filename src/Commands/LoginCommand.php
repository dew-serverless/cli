<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration\Repository;
use Dew\Cli\Contracts\Client;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'login', description: 'Authenticate with Dew')]
final class LoginCommand extends Command
{
    /**
     * Create a new command instance.
     */
    public function __construct(
        private Client $client,
        private Repository $config
    ) {
        parent::__construct();
    }

    /**
     * Executes the current command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (! $this->ensureTokenIsNotConfigured($io)) {
            return Command::SUCCESS;
        }

        $this->client->setToken($token = $this->askToken($io));
        $response = $this->client->user();

        if ($response->unauthorized()) {
            $io->error('The token is invalid.');

            return Command::FAILURE;
        }

        if ($response->error()) {
            $message = $response->json('message', 'Unknown error occurred.');
            $io->error('Failed to authenticate: '.$message);

            return Command::FAILURE;
        }

        $this->config->set('token', $token);
        $io->success(sprintf('You are logged in as %s.', $response->json('name')));

        return Command::SUCCESS;
    }

    /**
     * Ask the user to confirm overwriting an existing token if there is one.
     */
    private function ensureTokenIsNotConfigured(SymfonyStyle $io): bool
    {
        if ($this->config->has('token')) {
            return $io->confirm('The API token has already been configured. Do you want to overwrite it?', false);
        }

        return true;
    }

    /**
     * Ask the user for their Dew API token.
     */
    private function askToken(SymfonyStyle $io): string
    {
        return $io->askHidden('What is your Dew API token?', function (?string $token): string {
            if ($token === null || $token === '') {
                throw new \InvalidArgumentException('The token must not be empty.');
            }

            return $token;
        });
    }
}
