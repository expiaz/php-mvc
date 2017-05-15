<?php

namespace Core\Http;

use InvalidArgumentException;


class Response
{

    protected $protocol;

    protected $status = 200;

    protected $reasonPhrase = '';

    protected $contentLength = 0;

    protected $body;

    protected static $messages = [
        //Informational 1xx
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        //Successful 2xx
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        //Redirection 3xx
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        306 => '(Unused)',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        //Client Error 4xx
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a teapot',
        421 => 'Misdirected Request',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        444 => 'Connection Closed Without Response',
        451 => 'Unavailable For Legal Reasons',
        499 => 'Client Closed Request',
        //Server Error 5xx
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
        599 => 'Network Connect Timeout Error',
    ];

    const EOL = "\r\n";

    const CONTENT_LENGTH = 'Content-Length';
    const CONTENT_TYPE = 'Content-Type';
    const LOCATION = 'Location';

    public function __construct($protocol = 'HTTP/1.1', $status = 200, array $headers = [], string $body = '')
    {
        $this->protocol = $protocol;
        $this->status = $this->filterStatus($status);
        $this->headers = [
            static::CONTENT_LENGTH => '0',
            static::CONTENT_TYPE => 'text/html; charset=UTF-8'
        ];
        $this->body = $body;
        $this->contentLength = strlen($body);
    }


    public function getProtocol():string {
        return $this->protocol;
    }

    public function getStatusCode(): int
    {
        return $this->status;
    }

    public function getStatusPhrase():string{
        return $this->getReasonPhrase();
    }

    public function withStatus($code, $reasonPhrase = ''): Response
    {
        $code = $this->filterStatus($code);

        if (!is_string($reasonPhrase) && !method_exists($reasonPhrase, '__toString')) {
            throw new InvalidArgumentException('ReasonPhrase must be a string');
        }

        $this->status = $code;
        if ($reasonPhrase === '' && isset(static::$messages[$code])) {
            $reasonPhrase = static::$messages[$code];
        }

        if ($reasonPhrase === '') {
            throw new InvalidArgumentException('ReasonPhrase must be supplied for this code');
        }

        $this->reasonPhrase = $reasonPhrase;

        return $this;
    }

    protected function filterStatus($status):int
    {
        if (!is_integer($status) || $status<100 || $status>599) {
            throw new InvalidArgumentException('Invalid HTTP status code');
        }

        return $status;
    }

    public function getReasonPhrase(): string
    {
        if ($this->reasonPhrase) {
            return $this->reasonPhrase;
        }
        if (isset(static::$messages[$this->status])) {
            return static::$messages[$this->status];
        }
        return '';
    }




    public function withBody(string $body): Response
    {
        $this->body = $body;
        $this->contentLength = strlen($body);
        $this->withHeader(static::CONTENT_LENGTH, $this->contentLength);
        return $this;
    }

    public function getBody():string
    {
        return $this->body;
    }

    public function write(string $data): Response
    {
        return $this->append($data);
    }

    private function prepend(string $data):Response
    {
        $this->body = $data . $this->body;
        $this->contentLength += strlen($data);
        $this->withHeader(static::CONTENT_LENGTH, $this->contentLength);

        return $this;
    }

    private function append(string $data): Response
    {
        $this->body = $this->body . $data;
        $this->contentLength += strlen($data);
        $this->withHeader(static::CONTENT_LENGTH, $this->contentLength);
        return $this;
    }




    public function getHeaders(){
        return $this->headers;
    }

    public function getHeader($header){
        if($this->hasHeader($header)){
            return $this->headers[$header];
        }
        throw new \Exception("Response::getHeader no {$header}");
    }

    public function hasHeader($header){
        return isset($this->headers[$header]);
    }

    public function withHeader(string $header, string $value): Response
    {
        $this->headers[$header] = $value;
        return $this;
    }


    public function withRedirect($url, $status = null)
    {
        $this->withHeader(static::LOCATION, (string)$url);

        if (is_null($status) && $this->getStatusCode() === 200) {
            $status = 302;
        }

        if (!is_null($status)) {
            return $this->withStatus($status);
        }

        return $this;
    }


    public function withJson($data, $status = null, $encodingOptions = 0): Response
    {
        $this->write($json = json_encode($data, $encodingOptions));

        // Ensure that the json encoding passed successfully
        if ($json === false) {
            throw new \RuntimeException(json_last_error_msg(), json_last_error());
        }

        $responseWithJson = $this->withHeader('Content-Type', 'application/json;charset=utf-8');
        if (isset($status)) {
            return $responseWithJson->withStatus($status);
        }
        return $responseWithJson;
    }


    public function isEmpty():bool
    {
        return in_array($this->getStatusCode(), [204, 205, 304]);
    }


    public function isInformational():bool
    {
        return $this->getStatusCode() >= 100 && $this->getStatusCode() < 200;
    }


    public function isOk():bool
    {
        return $this->getStatusCode() === 200;
    }


    public function isSuccessful():bool
    {
        return $this->getStatusCode() >= 200 && $this->getStatusCode() < 300;
    }


    public function isRedirect():bool
    {
        return in_array($this->getStatusCode(), [301, 302, 303, 307]);
    }


    public function isRedirection():bool
    {
        return $this->getStatusCode() >= 300 && $this->getStatusCode() < 400;
    }


    public function isForbidden():bool
    {
        return $this->getStatusCode() === 403;
    }


    public function isNotFound():bool
    {
        return $this->getStatusCode() === 404;
    }


    public function isClientError()
    {
        return $this->getStatusCode() >= 400 && $this->getStatusCode() < 500;
    }


    public function isServerError():bool
    {
        return $this->getStatusCode() >= 500 && $this->getStatusCode() < 600;
    }


    public function __toString():string
    {
        $output = sprintf(
            '%s %s %s',
            $this->getProtocol(),
            $this->getReasonPhrase()
        );
        $output .= Response::EOL;
        foreach ($this->getHeaders() as $name => $value) {
            $output .= sprintf('%s: %s', $name, $value) . Response::EOL;
        }
        $output .= Response::EOL;
        $output .= (string)$this->getBody();

        return $output;
    }
}
