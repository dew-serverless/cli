<?php

namespace Dew\Cli\Commands;

use AlibabaCloud\SDK\FCOpen\V20210406\FCOpen;
use AlibabaCloud\SDK\FCOpen\V20210406\Models\InvokeFunctionRequest;
use AlibabaCloud\Tea\Utils\Utils;
use Darabonba\OpenApi\Models\Config;
use Dew\Cli\Concerns\ResolvesProject;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(name: 'cli', description: 'Run command remotely')]
class CliCommand extends Command
{
    use ResolvesProject;

    protected function configure(): void
    {
        $this->addArgument('cmd', InputArgument::REQUIRED, 'The command send remotely');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $project = $this->project();
        $credentials = $project->credentials();

        $fc = new FcOpen(new Config([
            'accessKeyId' => $credentials->keyId(),
            'accessKeySecret' => $credentials->keySecret(),
            'endpoint' => sprintf('%s.%s.fc.aliyuncs.com', $credentials->accountId(), $project->region()),
        ]));

        $response = $fc->invokeFunction($project->serviceName(), 'console', new InvokeFunctionRequest([
            'body' => Utils::toBytes(json_encode([
                'type' => 'cli',
                'command' => $input->getArgument('cmd'),
            ])),
        ]));

        $body = Utils::toString($response->body);
        $payload = json_decode($body, associative: true);

        $output->writeln($payload['output']);

        return $payload['status'];
    }
}