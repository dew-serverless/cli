<?php

namespace Dew\Cli;

use Dew\Cli\Contracts\CommunicatesWithDew;
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
        private OutputInterface $output
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
        $invocation = $this->dew->runCommand($this->projectId, $this->environment, $command);

        while ($invocation['data']['status'] === 'running') {
            sleep(1);

            $invocation = $this->dew->getCommand(
                $this->projectId, $this->environment, $invocation['data']['id']
            );
        }

        $this->output->writeln($invocation['data']['output']);

        return $invocation['data']['exit_code'] ?? Command::SUCCESS;
    }
}
