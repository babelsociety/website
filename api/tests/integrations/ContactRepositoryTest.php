<?php
namespace Integrations\BabelSociety;

use PHPUnit\Framework\TestCase;

use BabelSociety\ContactRepository;
use BabelSociety\Contact;
use PDO;

class ContactRepositoryTest extends TestCase {
    /** @var PDO */
    private $db;

    public function setUp(): void {
        parent::setUp();

        getDatabase()->exec('TRUNCATE contacts');
    }

    public function testExistsWillReturnTrueWhenContactIsPresent(): void {
        $repo = ContactRepository::get();

        $repo->store(new Contact(
            'test', 'er', 'test@exists.com',
            'location', 'description', 'contribution',
            true,
        ));

        $this->assertTrue(
            $repo->exists('test@exists.com'),
            'Contact should exists in the database'
        );
    }
}
