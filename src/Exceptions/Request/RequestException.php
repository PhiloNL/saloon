<?php

declare(strict_types=1);

namespace Saloon\Exceptions\Request;

use Saloon\Helpers\StatusCodeHelper;
use Throwable;
use Saloon\Contracts\Response;
use Saloon\Exceptions\SaloonException;

class RequestException extends SaloonException
{
    /**
     * The Saloon Response
     *
     * @var Response
     */
    protected Response $response;

    /**
     * Maximum length allowed for the body
     *
     * @var int
     */
    protected int $maxBodyLength = 200;

    /**
     * Create the RequestException
     *
     * @param \Saloon\Contracts\Response $response
     * @param string|null $message
     * @param $code
     * @param \Throwable|null $previous
     */
    public function __construct(Response $response, ?string $message = null, $code = 0, ?Throwable $previous = null)
    {
        $this->response = $response;

        if (is_null($message)) {
            $status = $this->getStatus();
            $statusCodeMessage = $this->getStatusMessage() ?? 'Unknown Status';
            $rawBody = $response->body();
            $exceptionBodyMessage = strlen($rawBody) > $this->maxBodyLength ? substr($rawBody, 0, $this->maxBodyLength) : $rawBody;

            $message = sprintf('%s (%s) - %s', $statusCodeMessage, $status, $exceptionBodyMessage);
        }

        parent::__construct($message, $code, $previous);
    }

    /**
     * Get the Saloon Response Class.
     *
     * @return Response
     */
    public function getResponse(): Response
    {
        return $this->response;
    }

    /**
     * Get the HTTP status code
     *
     * @return int
     */
    public function getStatus(): int
    {
        return $this->response->status();
    }

    /**
     * Get the status message
     *
     * @return string|null
     */
    public function getStatusMessage(): ?string
    {
        return StatusCodeHelper::getMessage($this->getStatus());
    }
}
