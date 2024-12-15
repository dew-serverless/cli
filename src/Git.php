<?php

declare(strict_types=1);

namespace Dew\Cli;

use Symfony\Component\Process\Process;

final class Git
{
    /**
     * Create a new git instance.
     */
    public function __construct(
        public ?string $hash,
        public ?string $authorName,
        public ?string $authorEmail,
        public ?string $subject,
        public ?string $branch,
        public ?bool $isDirty
    ) {
        //
    }

    /**
     * Create a git instance from the context.
     */
    public static function fromContext(): ?static
    {
        // %H: commit hash
        // %an: author name
        // %ae: author email
        // %s: subject
        // %n: new line
        $process = Process::fromShellCommandline(<<<'COMMAND'
        git show -s --format="%H%n%an%n%ae%n%s" \
            && git rev-parse --abbrev-ref HEAD \
            && git status --porcelain
        COMMAND);

        if ($process->run() !== 0) {
            return null;
        }

        $output = explode("\n", $process->getOutput());

        return new self(
            hash: $output[0],
            authorName: $output[1],
            authorEmail: $output[2],
            subject: $output[3],
            branch: $output[4] === 'HEAD' ? null : $output[4],
            isDirty: count($output) > 5,
        );
    }
}
