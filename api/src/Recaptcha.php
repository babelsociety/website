<?php
namespace BabelSociety;

use GuzzleHttp\Client;

class Recaptcha {
    use Singleton;

    /** @var string **/
    private $secret;

    /** @var string **/
    private $hostname;

    public function __construct(string $secret, string $hostname) {
        $this->secret = $secret;
        $this->hostname = $hostname;
    }

    private static function create(): self {
        $config = config('captcha');
        return new Recaptcha($config['secret'], $config['hostname']);
    }

    public function verifyRequest(array $req): ?Response {
        $failedCaptcha = empty($req['g-recaptcha-response']) 
            || !$this->verify($req['g-recaptcha-response']);


        return ($failedCaptcha)
            ? Response::error('Invalid reCAPTCHA, please verify it again')
            : null;
    }

    public function verify(string $response): bool {
        $client = new Client();

        $resp = $client->request(
            'POST',
            'https:/www.recaptcha.net/recaptcha/api/siteverify',
            [
                'timeout' => 1, // second
                'form_params' => [
                    'secret' => $this->secret,
                    'response' => $response,
                ]
            ]
        );


        if ($resp->getStatusCode() !== 200)
            return false;

        $body = json_decode($resp->getBody(), true);

        return isset($body['success'])
            && $body['success'] 
            && $body['hostname'] === $this->hostname;
    }
}
