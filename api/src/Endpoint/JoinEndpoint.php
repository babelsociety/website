<?php
namespace BabelSociety\Endpoint;

use BabelSociety\Contact;
use BabelSociety\ContactRepository;
use BabelSociety\HttpStatus;
use BabelSociety\Newsletter;
use BabelSociety\Parser;
use BabelSociety\Recaptcha;
use BabelSociety\Response;
use BabelSociety\Result\ErrResult;
use BabelSociety\Result\Result;

class JoinEndpoint {
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
        return chain($httpMethod === 'POST', new Response(HttpStatus::METHOD_NOT_ALLOWED))
            ->then(function () use ($rawRequest) {
                return $this->parseRequest($rawRequest);
            })
            ->foldOk(function (Contact $contact) {
                $alreadyExists = $this->repository->exists($contact->getEmail());

                if (!$alreadyExists)
                    $this->repository->store($contact);

                if ($contact->wantsNewsletter())
                    $this->newsletter->subscribe($contact->getEmail());

                return new Response(HttpStatus::CREATED);
            });
    }

    /**
     * @return Result<Response, Contact>
     */
    private function parseRequest(string $raw): Result {
        $json = json_decode($raw, true);

        if ($json === null)
            return new ErrResult(
                new Response(HttpStatus::NOT_ACCEPTABLE)
            );

        if (empty($json['toc']))
            return new ErrResult(
                Response::error('You must accept the Terms and Condition')
            );


        $failedCaptcha = empty($json['g-recaptcha-response']) 
            || !$this->captcha->verify($json['g-recaptcha-response']);

        if ($failedCaptcha)
            return new ErrResult(
                Response::error('Invalid reCAPTCHA, please verify it again')
            );

        return resultAll([
            'first' => $this->parser->reqField('First name', $json, 'firstName'),
            'last' => $this->parser->reqField('Last name', $json, 'lastName'),
            'email' => $this->parser
                            ->reqField('E-Mail', $json, 'email')
                            ->then(function (string $raw) {
                                return $this->parser->email($raw);
                            }),
            'loc' => $this->parser->optField('Location', $json, 'location'),
            'desc' => $this->parser->optField('Presentation', $json, 'description'),
            'cntr' => $this->parser->optField('Contribution', $json, 'contribution'),
            'news' => $this->parser->optFieldBool('Newsletter', $json, 'newsletter'),
        ], function (array $r) {
            return new Contact(
                $r['first'], $r['last'], $r['email'],
                $r['loc'], $r['desc'], $r['cntr'],
                $r['news']
            );
        });
    }
}
