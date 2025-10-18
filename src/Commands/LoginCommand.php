<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration\ArrayRepository;
use Dew\Cli\Configuration\Repository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Dew;
use GuzzleHttp\Exception\ClientException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'login', description: 'Authenticate with Dew')]
final class LoginCommand extends Command
{
    /**
     * The maximum number of login attempts.
     */
    private const MAX_ATTEMPTS = 3;

    /**
     * Create a new command instance.
     */
    public function __construct(
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
        $attempts = 0;

        while ($attempts < self::MAX_ATTEMPTS) {
            $attempts++;

            try {
                $token = $this->askToken($io);
                $user = $this->client($token)->user();
                $this->config->set('token', $token);
                $io->success(sprintf('You are logged in as %s.', $user['name']));

                return Command::SUCCESS;
            } catch (ClientException $e) {
                $status = $e->getResponse()->getStatusCode();

                if ($status === 401) {
                    $remaining = self::MAX_ATTEMPTS - $attempts;

                    $remaining > 0
                        ? $io->error(sprintf('The token is invalid. You have %d attempt(s) left.', $remaining))
                        : $io->error('The token is invalid.');

                    continue;
                }

                $contents = (string) $e->getResponse()->getBody();
                $decoded = json_decode($contents, associative: true);
                $message = $decoded['message'] ?? 'Unknown error occurred.';
                $io->error('Failed to authenticate: '.$message);

                return Command::FAILURE;
            }
        }

        return Command::FAILURE;
    }

    /**
     * Ask the user for their Dew API token.
     */
    private function askToken(SymfonyStyle $io): string
    {
        return $io->askHidden('What is your Dew API token', function (?string $token): string {
            if ($token === null || $token === '') {
                throw new \RuntimeException('The token must not be empty.');
            }

            return $token;
        });
    }

    /**
     * Create a client for token validation.
     */
    private function client(string $token): Client
    {
        return Dew::make(
            new ArrayRepository(['token' => $token])
        );
    }
}
