<?php
namespace Integrations\BabelSociety;

use BabelSociety\Recaptcha;
use PHPUnit\Framework\TestCase;

/**
 * @group manual
 */
class RecaptchaTest extends TestCase {
    /**
     * This sandbox key is provided by google and will always succeed
     */
    const SANDBOX_SECRET = '6LeIxAcTAAAAAGG-vFI1TnRWxMZNFuojJ4WifJWe';
    const SANDBOX_HOST = 'testkey.google.com';

    public function testValidFlow() {
        $this->assertTrue(
            $this->verify(),
            'Validation should have succeeded'
        );
    }

    public function testFailOnWrongHostname() {
        $this->assertFalse(
            $this->verify('wrong'),
            'Validation should have failed'
        );
    }

    /**
     * To run this test you must provide a valid secret
     */
    public function testFailOnWrongResponse() {
        $captcha = new Recaptcha(getenv('RECAPTCHA_SECRET'), '127.0.0.1');
        $this->assertFalse(
            $captcha->verify('wrong'),
            'Validation should have failed'
        );
    }

    private function verify($host = self::SANDBOX_HOST): bool {
        $captcha = new Recaptcha(self::SANDBOX_SECRET, $host);

        return $captcha->verify('always succeeded');
    }
}
