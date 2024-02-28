<?php

namespace Dew\Cli;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Output\OutputInterface;

final class ExecuteCommand
{
    use InteractsWithDew;

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
        $commandId = $this->runCommand($command)['data']['id'];

        while (true) {
            $invocation = $this->getCommand($commandId);

            if ($invocation['data']['status'] !== 'running') {
                $this->output->writeln($invocation['data']['output']);

                return $invocation['data']['exit_code'] ?? Command::SUCCESS;
            }

            sleep(1);
        }
    }

    /**
     * Run the command on the environment.
     */
    private function runCommand(string $command): array
    {
        $response = Client::make()
            ->post('/api/projects/'.$this->projectId.'/environments/'.$this->environment.'/commands', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->token),
                ],
                'json' => [
                    'command' => $command,
                ],
            ]);

        return json_decode($response->getBody()->getContents(), associative: true);
    }

    /**
     * Get the command invocation data.
     */
    private function getCommand(int $commandId): array
    {
        $response = Client::make()
            ->get('/api/projects/'.$this->projectId.'/environments/'.$this->environment.'/commands/'.$commandId, [
                'headers' => [
                    'Accept' => 'application/json',
                    'Authorization' => sprintf('Bearer %s', $this->token),
                ],
            ]);

        return json_decode($response->getBody()->getContents(), associative: true);
    }
}
