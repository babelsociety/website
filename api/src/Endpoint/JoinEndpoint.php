<?php
namespace BabelSociety\Endpoint;

use BabelSociety\Contact;
use BabelSociety\ContactRepository;
use BabelSociety\Newsletter;
use BabelSociety\Parser;
use BabelSociety\Recaptcha;
use BabelSociety\Response;
use BabelSociety\Result\Result;

class JoinEndpoint implements Endpoint {
    /** @var ContactRepository */
    private $repository;

    /** @var Newsletter */
    private $newsletter;

    /** @var Recaptcha **/
    private $captcha;

    /** @var Parser */
    private $parser;

    public function __construct(
        ContactRepository $repository,
        Newsletter $newsletter,
        Recaptcha $captcha
    ) {
        $this->repository = $repository;
        $this->newsletter = $newsletter;
        $this->captcha = $captcha;
        $this->parser = new Parser();
    }

    public static function create(): self {
        return new JoinEndpoint(
            ContactRepository::get(),
            Newsletter::get(),
            Recaptcha::get()
        );
    }

    public function invoke(string $httpMethod, string $rawRequest): Response {
        return chain($httpMethod === 'POST', Response::methodNotAllowed())
            ->then(function () use ($rawRequest) {
                return $this->parseRequest($rawRequest);
            })
            ->foldOk(function (Contact $contact) {
                $alreadyExists = $this->repository->exists($contact->getEmail());

                if (!$alreadyExists)
                    $this->repository->store($contact);

                if ($contact->wantsNewsletter())
                    $this->newsletter->subscribe($contact->getEmail());

                return Response::created();
            });
    }

    /**
     * @return Result<Response, Contact>
     */
    private function parseRequest(string $raw): Result {
        return $this
            ->parser->parseJson($raw)
            ->chain(function (array $req) {
                return empty($req['toc'])
                    ? Response::error('You must accept the Terms and Condition')
                    : null;
            })
            ->chain(function (array $req) {
                return $this->captcha->verifyRequest($req);
            })
            ->then(function (array $req) {
                return resultAll([
                    'first' => $this->parser->reqField('First name', $req, 'firstName'),
                    'last' => $this->parser->reqField('Last name', $req, 'lastName'),
                    'email' => $this->parseEmail($req),
                    'loc' => $this->parser->optField('Location', $req, 'location'),
                    'desc' => $this->parser->optField('Presentation', $req, 'description'),
                    'cntr' => $this->parser->optField('Contribution', $req, 'contribution'),
                    'news' => $this->parser->optFieldBool('Newsletter', $req, 'newsletter'),
                ], function (array $r) {
                    return new Contact(
                        $r['first'], $r['last'], $r['email'],
                        $r['loc'], $r['desc'], $r['cntr'],
                        $r['news']
                    );
                });
            });
    }

    private function parseEmail(array $req): Result {
        return $this
            ->parser
            ->reqField('E-Mail', $req, 'email')
            ->then(function (string $raw) {
                return $this->parser->email($raw);
            });
    }
}
