<?php

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
        echo "Run build steps\n";

        $config = $deployment->config->get($deployment->environment);
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
        $process = Process::fromShellCommandLine($command, $cwd);

        $process->run(function ($type, $buffer): void {
            echo $buffer;
        });
    }
}