<?php
namespace BabelSociety;

use \MailchimpMarketing\ApiClient;

class Mailchimp {
    use Singleton;

    /** @var ApiClient */
    private $client;

    /** @var string */
    private $listId;

    public function __construct(ApiClient $client, string $listId) {
        $this->client = $client;
        $this->listId = $listId;
    }

    private static function create(): self {
        $config = config('mailchimp');

        $client = new ApiClient();
        $client->setConfig([
            'apiKey' => $config['apiKey'],
            'server' => $config['server'],
        ]);

        return new Mailchimp($client, $config['listId']);
    }

    public function subscribe(string $email): void {
        $hash = md5(strtolower($email));

        $this->client->lists->setListMember($this->listId, $hash, [
            'email_address' => $email,
            'status_if_new' => 'pending',
            'language' => 'en',
        ]);
    }
}
