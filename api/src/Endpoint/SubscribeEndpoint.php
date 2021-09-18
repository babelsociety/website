<?php
namespace BabelSociety\Endpoint;

use BabelSociety\Newsletter;
use BabelSociety\Parser;
use BabelSociety\Recaptcha;
use BabelSociety\Response;
use BabelSociety\Result\Result;

class SubscribeEndpoint implements Endpoint {
    /** @var Newsletter **/
    private $newsletter;

    /** @var Recaptcha **/
    private $captcha;

    /** @var Parser */
    private $parser;

    public function __construct(
        Newsletter $newsletter,
        Recaptcha $captcha
    ) {
        $this->newsletter = $newsletter;
        $this->captcha = $captcha;
        $this->parser = new Parser();
    }

    public static function create(): self {
        return new SubscribeEndpoint(
            Newsletter::get(),
            Recaptcha::get()
        );
    }

    public function invoke(string $httpMethod, string $rawRequest): Response {
        return chain($httpMethod === 'POST', Response::methodNotAllowed())
            ->then(function () use ($rawRequest) {
                return $this->parseRequest($rawRequest);
            })
            ->foldOk(function (string $email) {
                $this->newsletter->subscribe($email);

                return Response::created();
            });
    }

    /**
     * @return Result<Response, string>
     */
    private function parseRequest(string $raw): Result {
        return $this
            ->parser->parseJson($raw)
            ->chain(function (array $req) {
                return $this->captcha->verifyRequest($req);
            })
            ->then(function (array $req) {
                return $this->parseEmail($req);
            });
    }

    /**
     * @return Result<Response, string>
     */
    private function parseEmail(array $req): Result {
        return $this
            ->parser
            ->reqField('E-Mail', $req, 'email')
            ->then(function (string $raw) {
                return $this->parser->email($raw);
            });
    }
}
