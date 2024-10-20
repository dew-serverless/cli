<?php

declare(strict_types=1);

namespace Dew\Cli\Deployments;

use Dew\Cli\Deployment;
use Symfony\Component\Process\Process;

class RunBuildSteps
{
    /**
     * Execute the job.
     */
    public function __invoke(Deployment $deployment): Deployment
    {
        $deployment->output?->writeln('Run build steps');

        $config = $deployment->config->getEnvironment($deployment->environment);
        $steps = $config['build'] ?? [];

        if (empty($steps)) {
            return $deployment;
        }

        foreach ($steps as $command) {
            $this->execute($command, $deployment->buildDir());
        }

        return $deployment;
    }

    /**
     * Execute the command.
     */
    protected function execute(string $command, ?string $cwd = null): void
    {
        $process = Process::fromShellCommandline($command, $cwd);

        $process->run(function ($type, $buffer): void {
            echo $buffer;
        });
    }
}
