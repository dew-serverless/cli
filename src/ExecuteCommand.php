<?php

declare(strict_types=1);

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
use Dew\Cli\Models\Command as Model;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

final class ExecuteCommand
{
    /**
     * The project ID.
     */
    public int $projectId;

    /**
     * The execution environment.
     */
    public string $environment;

    /**
     * Create a new execute command action.
     */
    public function __construct(
        private CommunicatesWithDew $dew,
        private ?OutputInterface $output = null
    ) {
        //
    }

    /**
     * Configure project ID.
     */
    public function forProject(int $projectId): self
    {
        $this->projectId = $projectId;

        return $this;
    }

    /**
     * Configure execution environment.
     */
    public function on(string $environment): self
    {
        $this->environment = $environment;

        return $this;
    }

    /**
     * Execute the given command.
     */
    public function execute(string $command): int
    {
        $invocation = $this->run($command);

        while ($invocation->isRunning()) {
            sleep(1);

            $invocation = $this->get($invocation->id);
        }

        $this->output?->writeln($invocation->output);

        return $invocation->exit_code ?? Command::SUCCESS;
    }

    private function run(string $command): Model
    {
        return $this->dew->runCommand($this->projectId, $this->environment, $command);
    }

    private function get(string $commandId): Model
    {
        return $this->dew->getCommand($this->projectId, $this->environment, $commandId);
    }
}
