<?php
namespace Units\BabelSociety\Endpoint;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use BabelSociety\Endpoint\SubscribeEndpoint;

use BabelSociety\ContactRepository;
use BabelSociety\HttpStatus;
use BabelSociety\Newsletter;
use BabelSociety\Recaptcha;
use BabelSociety\Response;

class SubscribeEndpointTest extends TestCase {
    public function testReturnsErrorOnWrongHttpMethod(): void {
        $this->assertEquals(
            Response::methodNotAllowed(),
            $this->endpoint()->invoke('GET', '')
        );
    }

    public function testReturnsErrorOnInvalidJSON(): void {
        $this->assertEquals(
            Response::notAcceptable(),
            $this->endpoint()->invoke('POST', 'nope')
        );
    }

    public function testReturnsErrorOnWrongCaptcha(): void {
        $this->assertValidationError(
            ['g-recaptcha-response' => 'wrong'],
            'reCAPTCHA'
        );
    }

    /**
     * @dataProvider dataProviderInvalidEmail
     */
    public function testReturnsErrorOnInvalidEmail(string $email): void {
        $this->assertValidationError(
            ['email' => $email],
            'E-Mail'
        );
    }

    public function dataProviderInvalidEmail(): array {
        return [
            'Empty' => [''],
            'All blanks' => ['  '],
            'Starts with blanks' => [' test@email.com'],
            'Ends with blanks' => ['test@email.com '],
            'Multiple @' => ['test@email@com'],
            'No local part' => ['@email.com '],
            'No domain' => ['test@.com'],
        ];
    }

    public function testCorrectRequestWillSucceed(): void {
        $this->assertEquals(
            Response::created(),
            $this->execRequest([
                'g-recaptcha-response' => 'correct',
                'email' => 'test@email.com',
            ])
        );
    }

    private function assertValidationError(array $override, string $expected): void {
        $req = array_merge([
            'g-recaptcha-response' => 'correct',
            'email' => 'test@email.com',
        ], $override);

        $resp = $this->execRequest($req);
        $this->assertEquals(
            HttpStatus::VALIDATION_ERROR,
            $resp->getCode()
        );
        $this->assertStringContainsString(
            $expected,
            $resp->getData()['error']
        );
    }

    private function execRequest(array $req): Response {
        return $this->endpoint()->invoke('POST', json_encode($req));
    }

    private function endpoint(): SubscribeEndpoint {
        $captcha = $this
            ->getMockBuilder(Recaptcha::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['verify'])
            ->getMock();

        $captcha
            ->method('verify')
            ->willReturnCallback(function (string $resp) {
                return $resp === 'correct';
            });

        return new SubscribeEndpoint(
            $this->createMock(Newsletter::class),
            $captcha
        );
    }
}
