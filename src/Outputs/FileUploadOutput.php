<?php

declare(strict_types=1);

namespace Dew\Cli\Outputs;

use Dew\Cli\Filesize\Filesize;
use Dew\Cli\Filesize\Size;
use Symfony\Component\Console\Output\OutputInterface;

final class FileUploadOutput
{
    private Size $totalBytes;

    public function __construct(
        private OutputInterface $output,
        private string $file,
        private int $total
    ) {
        $this->totalBytes = Filesize::make($this->total);
    }

    public function update(int $uploaded): void
    {
        $u = $this->totalBytes::fromByte($uploaded);

        $this->clear();
        $this->write(sprintf('%s  %s %s / %s %s',
            $this->file,
            $u->round(2),
            $u->symbol(),
            $this->totalBytes->round(2),
            $this->totalBytes->symbol()
        ));
    }

    public function complete(): void
    {
        $this->clear();
        $this->writeln(sprintf('%s  %s %s',
            $this->file,
            $this->totalBytes->round(2),
            $this->totalBytes->symbol()
        ));
    }

    private function write(string $buffer): void
    {
        $this->output->write($buffer);
    }

    private function writeln(string $buffer): void
    {
        $this->output->writeln($buffer);
    }

    private function clear(): void
    {
        $this->output->write("\x1b[1K\r");
    }
}
