<?php

declare(strict_types=1);

namespace Dew\Cli;

use Dew\Cli\Configuration\Repository;
use Symfony\Component\Console\Style\SymfonyStyle;

trait RequiresAuthentication
{
    /**
     * Ensure the user is authenticated.
     */
    protected function ensureAuthenticated(SymfonyStyle $io, Repository $config): bool
    {
        if ($config->has('token')) {
            return true;
        }

        $io->error('You must be logged in to perform this action');
        $io->block('Run "dew login" to authenticate');

        return false;
    }
}
