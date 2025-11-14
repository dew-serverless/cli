<?php

declare(strict_types=1);

namespace Dew\Cli\Http;

use Psr\Http\Message\ResponseInterface;

/**
 * @template T of array<string, mixed>
 */
final class Response
{
    use DeterminesStatus;

    /**
     * The decoded data.
     *
     * @var T|null
     */
    private ?array $decoded = null;

    /**
     * Create a response instance.
     */
    public function __construct(
        private ResponseInterface $response
    ) {
        //
    }

    /**
     * The status code.
     */
    public function status(): int
    {
        return $this->response->getStatusCode();
    }

    /**
     * Get the raw response contents.
     */
    public function body(): string
    {
        return (string) $this->response->getBody();
    }

    /**
     * Get the decoded data.
     *
     * @param  key-of<T>|string|null  $key
     * @return ($key is null ? T : mixed)
     */
    public function json(?string $key = null, mixed $default = null): mixed
    {
        $this->decoded = is_array($this->decoded)
            ? $this->decoded
            : json_decode($this->body(), associative: true);

        return data_get($this->decoded, $key, $default);
    }

    /**
     * Get the underlying PSR response.
     */
    public function toPsrResponse(): ResponseInterface
    {
        return $this->response;
    }
}
