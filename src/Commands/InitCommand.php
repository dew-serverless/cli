<?php

declare(strict_types=1);

namespace Dew\Cli\Commands;

use Dew\Cli\Configuration\Repository;
use Dew\Cli\Contracts\Client;
use Dew\Cli\Manifest;
use Dew\Cli\RequiresAuthentication;
use Illuminate\Support\Str;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(name: 'init', description: 'Initialize the project')]
final class InitCommand extends Command
{
    use RequiresAuthentication;

    /**
     * Create a command instance.
     */
    public function __construct(
        private Client $client,
        private Repository $config,
        private Manifest $manifest,
        private string $rootPath
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

        if ($this->manifest->exists()) {
            $io->note('Project is already initialized.');

            return Command::FAILURE;
        }

        $choice = $io->choice(
            'Link to an existing project or create a new one?',
            ['link', 'create'],
            'create'
        );

        return match ($choice) {
            'link' => $this->linkProject($io),
            'create' => $this->createProject($io),
            default => Command::FAILURE,
        };
    }

    /**
     * Link to an existing project.
     */
    private function linkProject(SymfonyStyle $io): int
    {
        $response = $this->client->listProjects();

        if ($response->error()) {
            $io->error(sprintf('Could not retrieve projects: %s',
                $response->json('message', 'Unknown error occurred.')
            ));

            return Command::FAILURE;
        }

        $projects = $response->json('data', []);

        if (empty($projects)) {
            $io->warning('No projects found.');

            if ($io->confirm('Would you like to create a new project?')) {
                return $this->createProject($io);
            }

            return Command::SUCCESS;
        }

        $choices = array_map(fn (array $project) => $project['slug'], $projects);
        $slug = $io->choice('Select a project to link', $choices);
        $index = array_flip($choices)[$slug];
        $project = $projects[$index];

        $io->table(
            ['ID', 'Name', 'Slug', 'Region'],
            [[$project['id'], $project['name'], $project['slug'], $project['region']]]
        );

        $confirm = $io->confirm('Does it look correct?');

        if (! $confirm) {
            return Command::SUCCESS;
        }

        $this->manifest->write([
            'id' => $project['id'],
        ]);

        $io->success('Project successfully linked!');

        return Command::SUCCESS;
    }

    /**
     * Create a new project.
     */
    private function createProject(SymfonyStyle $io): int
    {
        $name = $this->askName($io);
        $slug = $this->askSlug($io, $name);
        $region = $this->askRegion($io);

        $io->table(
            ['Name', 'Slug', 'Region'],
            [[$name, $slug, $region]]
        );

        $confirm = $io->confirm('Does it look correct?');

        if (! $confirm) {
            return Command::SUCCESS;
        }

        $response = $this->client->createProject([
            'name' => $name,
            'slug' => $slug,
            'region' => $region,
        ]);

        if ($response->error()) {
            $io->error(sprintf('Could not create project: %s',
                $response->json('message', 'Unknown error occurred.')
            ));

            return Command::FAILURE;
        }

        $this->manifest->write([
            'id' => $response->json('data.id'),
        ]);

        $io->success('Project successfully created!');

        return Command::SUCCESS;
    }

    /**
     * Ask for the project name.
     */
    private function askName(SymfonyStyle $io): string
    {
        return $io->ask('The project name', basename($this->rootPath), function (?string $value): string {
            if ($value === null || $value === '') {
                throw new \RuntimeException('Project name cannot be empty.');
            }

            if (strlen($value) > 16) {
                throw new \RuntimeException('Project name cannot exceed 16 characters.');
            }

            return $value;
        });
    }

    /**
     * Ask for the project slug.
     */
    private function askSlug(SymfonyStyle $io, string $name): string
    {
        return $io->ask('The project slug (unique identifier)', Str::slug($name), function (?string $value): string {
            if ($value === null || $value === '') {
                throw new \RuntimeException('Project slug cannot be empty.');
            }

            if (strlen($value) > 16) {
                throw new \RuntimeException('Project slug cannot exceed 16 characters.');
            }

            if (preg_match('/^[a-z0-9-]+$/', $value) !== 1) {
                throw new \RuntimeException('Project slug must only contain lowercase letters, numbers, and hyphens.');
            }

            $response = $this->client->checkProjectSlug($value);

            if ($response->error()) {
                throw new \RuntimeException('Could not validate project slug. Please try again.');
            }

            if (! $response->json('data.available')) {
                throw new \RuntimeException('Project slug is already taken.');
            }

            return $value;
        });
    }

    /**
     * Ask for the project region.
     */
    private function askRegion(SymfonyStyle $io): string
    {
        $response = $this->client->regions();

        if ($response->error()) {
            throw new \RuntimeException(sprintf('Could not retrieve regions: %s',
                $response->json('message', 'Unknown error occurred.')
            ));
        }

        $availableRegions = $response->json('data', []);

        if ($availableRegions === []) {
            throw new \RuntimeException('No regions available. Please try again later.');
        }

        return $io->choice('The project region', $availableRegions, $availableRegions[0]);
    }
}
