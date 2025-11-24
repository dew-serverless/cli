<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration\Repository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\InteractsWithManifest;
use Dew\Cli\Manifest;
use Dew\Cli\RequiresAuthentication;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'connect', description: 'Connect the project with Alibaba Cloud')]
final class ConnectCommand extends Command
{
    use InteractsWithManifest;
    use RequiresAuthentication;

    /**
     * Create a command instance.
     */
    public function __construct(
        private Client $client,
        private Repository $config,
        private Manifest $manifest
    ) {
        parent::__construct();
    }

    /**
     * Execute the command.
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        if (! $this->ensureAuthenticated($io, $this->config)) {
            return Command::FAILURE;
        }

        if (! $this->ensureProjectInitialized($io, $this->manifest)) {
            return Command::FAILURE;
        }

        $response = $this->client->connectAcsAccount($this->manifest->get('id'), [
            'access_key_id' => $this->askKeyId($io),
            'access_key_secret' => $this->askKeySecret($io),
        ]);

        if ($response->error()) {
            $io->error(sprintf(
                'Could not connect to Alibaba Cloud: %s',
                $response->json('message', 'Unknown error occurred.')
            ));

            return Command::FAILURE;
        }

        $io->success('Successfully connected Alibaba Cloud account');

        return Command::SUCCESS;
    }

    /**
     * Ask for the Access Key ID.
     */
    private function askKeyId(SymfonyStyle $io): string
    {
        return $io->ask('Access Key ID', validator: function (?string $value): string {
            if ($value === null || $value === '') {
                throw new \RuntimeException('Access Key ID is required');
            }

            return $value;
        });
    }

    /**
     * Ask for the Access Key Secret.
     */
    private function askKeySecret(SymfonyStyle $io): string
    {
        return $io->askHidden('Access Key Secret', validator: function (?string $value): string {
            if ($value === null || $value === '') {
                throw new \RuntimeException('Access Key Secret is required');
            }

            return $value;
        });
    }
}
