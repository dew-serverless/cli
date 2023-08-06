<?php

namespace Dew\Cli\Tests;

use PHPUnit\Framework\Assert;
use Symfony\Component\Console\Style\StyleInterface;

class FakeStyle implements StyleInterface
{
    /**
     * The collected buffer.
     */
    protected array $buffer = [];

    /**
     * The collected tables.
     */
    protected array $tables = [];

    /**
     * The collected questions.
     */
    protected array $questions = [];

    /**
     * The collected confirmations.
     */
    protected array $confirmations = [];

    /**
     * Formats a command title.
     */
    public function title(string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a section title.
     */
    public function section(string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a list.
     */
    public function listing(array $elements): void
    {
        $this->assertBufferHas($elements);
    }

    /**
     * Formats informational text.
     */
    public function text(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a success result bar.
     */
    public function success(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats an error result bar.
     */
    public function error(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a warning result bar.
     */
    public function warning(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a note admonition.
     */
    public function note(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a caution admonition.
     */
    public function caution(array|string $message): void
    {
        $this->assertBufferHas($message);
    }

    /**
     * Formats a table.
     */
    public function table(array $headers, array $rows): void
    {
        $header = implode('|', $headers);

        Assert::assertArrayHasKey($header, $this->tables, 'Unexpected table.');

        foreach ($rows as $row) {
            Assert::assertContains(implode('|', $row), $this->tables[$header], 'Unexpected table row.');
        }
    }

    /**
     * Asks a question.
     */
    public function ask(string $question, string $default = null, callable $validator = null): mixed
    {
        Assert::assertArrayHasKey($question, $this->questions, 'Unexpected question.');

        return $this->questions[$question] ?? $default;
    }

    /**
     * Asks a question with the user input hidden.
     */
    public function askHidden(string $question, callable $validator = null): mixed
    {
        Assert::assertArrayHasKey($question, $this->questions, 'Unexpected question.');

        return $this->questions[$question];
    }

    /**
     * Asks for confirmation.
     */
    public function confirm(string $question, bool $default = true): bool
    {
        Assert::assertArrayHasKey($question, $this->confirmations, 'Unexpected confirmation.');

        return $this->confirmations[$question] ?? $default;
    }

    /**
     * Asks a choice question.
     */
    public function choice(string $question, array $choices, mixed $default = null): mixed
    {
        Assert::assertArrayHasKey($question, $this->questions, 'Unexpected question.');

        return $this->questions[$question] ?? $default;
    }

    /**
     * Add newline(s).
     */
    public function newLine(int $count = 1): void
    {
        //
    }

    /**
     * Starts the progress output.
     */
    public function progressStart(int $max = 0): void
    {
        //
    }

    /**
     * Advances the progress output X steps.
     */
    public function progressAdvance(int $step = 1): void
    {
        //
    }

    /**
     * Finishes the progress output.
     */
    public function progressFinish(): void
    {
        //
    }

    /**
     * Assert buffer contains the given messages.
     */
    protected function assertBufferHas(array|string $messages): void
    {
        $messages = is_array($messages) ? $messages : [$messages];

        foreach ($messages as $message) {
            Assert::assertContains($message, $this->buffer, 'Unexpected output.');
        }
    }

    /**
     * Collect an output.
     */
    public function expectsOutput(string $message): self
    {
        $this->buffer[] = $message;

        return $this;
    }

    /**
     * Collect a table.
     */
    public function expectsTable(array $headers, array $rows): self
    {
        $header = implode('|', $headers);

        $this->tables[$header] = array_map(fn ($row) => implode('|', $row), $rows);

        return $this;
    }

    /**
     * Collect a question.
     */
    public function expectsQuestion(string $question, string $answer): self
    {
        $this->questions[$question] = $answer;

        return $this;
    }

    /**
     * Collect a confirmation.
     */
    public function expectsConfirmation(string $question, bool $answer): self
    {
        $this->confirmations[$question] = $answer;

        return $this;
    }
}