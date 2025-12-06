<?php

declare(strict_types=1);

namespace Dew\Cli;

use Symfony\Component\Console\Style\SymfonyStyle;

trait InteractsWithManifest
{
    /**
     * Ensure the project ID exists.
     */
    public function ensureProjectInitialized(SymfonyStyle $io, Manifest $manifest): bool
    {
        if (! $manifest->exists()) {
            return $this->projectIsntInitialized($io);
        }

        $projectId = $manifest->get('id');

        if ($projectId === null || ! is_int($projectId)) {
            return $this->projectIsntInitialized($io);
        }

        return true;
    }

    /**
     * Handle uninitialized project.
     */
    private function projectIsntInitialized(SymfonyStyle $io): bool
    {
        $io->error('Project is not initialized');
        $io->block('Run "dew init" to initialize the project');

        return false;
    }
}
