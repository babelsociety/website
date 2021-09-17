<?php
namespace BabelSociety;

use PDO;
use GuzzleHttp\Exception\ClientException;

class Newsletter {
    use Singleton;

    /** @var PDO **/
    private $db;

    /** @var Mailchimp **/
    private $mailchimp;

    public function __construct(
        Mailchimp $mailchimp,
        PDO $db
    ) {
        $this->db = $db;
        $this->mailchimp = $mailchimp;
    }

    private static function create(): self {
        return new Newsletter(
            Mailchimp::get(),
            getDatabase()
        );
    }

    public function subscribe(string $email): void {
        $this->db
             ->prepare('INSERT IGNORE INTO newsletter(email) VALUES (?)')
             ->execute([$email]);

        try {
            $this->mailchimp->subscribe($email);
        }
        catch (ClientException $ex) {
            logger()->error('Cannot subscribe to Mailchimp', [
                'email' => $email,
                'httpStatus' => $ex->getResponse()->getStatusCode(),
                'httpBody' => json_decode($ex->getResponse()->getBody(), true),
            ]);
        }
    }
}
