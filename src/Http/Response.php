<?php

declare(strict_types=1);

namespace Saloon\Http;

use Throwable;
use Saloon\Traits\Macroable;
use Saloon\Contracts\Request;
use Saloon\Repositories\ArrayStore;
use Saloon\Contracts\PendingRequest;
use Psr\Http\Message\StreamInterface;
use Psr\Http\Message\ResponseInterface;
use Saloon\Traits\Responses\HasResponseHelpers;
use Saloon\Contracts\Response as ResponseContract;

class Response implements ResponseContract
{
    use Macroable;
    use HasResponseHelpers;

    /**
     * The PSR response from the sender.
     *
     * @var ResponseInterface|mixed
     */
    protected ResponseInterface $psrResponse;

    /**
     * The pending request that has all the request properties
     *
     * @var PendingRequest
     */
    protected PendingRequest $pendingRequest;

    /**
     * The original sender exception
     *
     * @var Throwable|null
     */
    protected ?Throwable $senderException = null;

    /**
     * Create a new response instance.
     *
     * @param PendingRequest $pendingRequest
     * @param ResponseInterface $psrResponse
     * @param Throwable|null $senderException
     */
    public function __construct(ResponseInterface $psrResponse, PendingRequest $pendingRequest, Throwable $senderException = null)
    {
        $this->psrResponse = $psrResponse;
        $this->pendingRequest = $pendingRequest;
        $this->senderException = $senderException;
    }

    /**
     * Create a new response instance
     *
     * @param \Saloon\Contracts\PendingRequest $pendingRequest
     * @param \Psr\Http\Message\ResponseInterface $psrResponse
     * @param \Throwable|null $senderException
     * @return $this
     */
    public static function fromPsrResponse(ResponseInterface $psrResponse, PendingRequest $pendingRequest, ?Throwable $senderException = null): static
    {
        return new static($psrResponse, $pendingRequest, $senderException);
    }

    /**
     * Get the pending request that created the response.
     *
     * @return PendingRequest
     */
    public function getPendingRequest(): PendingRequest
    {
        return $this->pendingRequest;
    }

    /**
     * Get the original request that created the response.
     *
     * @return Request
     */
    public function getRequest(): Request
    {
        return $this->pendingRequest->getRequest();
    }

    /**
     * Get the body of the response as string.
     *
     * @return string
     */
    public function body(): string
    {
        return (string)$this->stream();
    }

    /**
     * Get the body as a stream. Don't forget to close the stream after using ->close().
     *
     * @return StreamInterface
     */
    public function stream(): StreamInterface
    {
        return $this->psrResponse->getBody();
    }

    /**
     * Get the headers from the response.
     *
     * @return ArrayStore
     */
    public function headers(): ArrayStore
    {
        $headers = array_map(static function (array $header) {
            return count($header) === 1 ? $header[0] : $header;
        }, $this->psrResponse->getHeaders());

        return new ArrayStore($headers);
    }

    /**
     * Get the status code of the response.
     *
     * @return int
     */
    public function status(): int
    {
        return $this->psrResponse->getStatusCode();
    }

    /**
     * Create a PSR response from the raw response.
     *
     * @return ResponseInterface
     */
    public function getPsrResponse(): ResponseInterface
    {
        return $this->psrResponse;
    }

    /**
     * Get the original sender exception
     *
     * @return Throwable|null
     */
    public function getSenderException(): ?Throwable
    {
        return $this->senderException;
    }
}
