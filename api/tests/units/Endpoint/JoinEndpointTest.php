<?php
namespace Units\BabelSociety\Endpoint;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\MockObject\MockObject;

use BabelSociety\Endpoint\JoinEndpoint;

use BabelSociety\ContactRepository;
use BabelSociety\HttpStatus;
use BabelSociety\Newsletter;
use BabelSociety\Recaptcha;
use BabelSociety\Response;

class JoinEndpointTest extends TestCase {
    /** @var MockObject|ContactRepository **/
    private $repo;

    public function setUp(): void {
        parent::setUp();

        $this->repo = $this->createMock(ContactRepository::class);
    }

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

    public function testReturnsErrorOnMissingTermsAndCond(): void {
        $this->assertValidationError(
            ['toc' => false],
            'Terms and Condition'
        );
    }

    public function testReturnsErrorOnWrongCaptcha(): void {
        $this->assertValidationError(
            ['g-recaptcha-response' => 'wrong'],
            'reCAPTCHA'
        );
    }

    /**
     * @dataProvider dataProviderInvalidName
     */
    public function testReturnsErrorOnInvalidFirstName(string $name): void {
        $this->assertValidationError(
            ['firstName' => $name],
            'First name'
        );
    }

    /**
     * @dataProvider dataProviderInvalidName
     */
    public function testReturnsErrorOnInvalidLastName(string $name): void {
        $this->assertValidationError(
            ['lastName' => $name],
            'Last name'
        );
    }

    public function dataProviderInvalidName(): array {
        return [
            'Empty' => [''],
            'All blanks' => ['  '],
            'Starts with blanks' => [' test'],
            'Ends with blanks' => ['test '],
        ];
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

    public function testMinimalRequestWillSucceed(): void {
        $this->mockAlreadyExists(true);

        $this->assertEquals(
            new Response(HttpStatus::CREATED),
            $this->execRequest([
                'toc' => true,
                'g-recaptcha-response' => 'correct',
                'firstName' => 'Test',
                'lastName' => 'Er',
                'email' => 'test@email.com',
            ])
        );
    }

    private function mockAlreadyExists(bool $state): void {
        $this->repo
            ->method('exists')
            ->willReturn($state);

    }

    private function assertValidationError(array $override, string $expected): void {
        $req = array_merge([
            'toc' => true,
            'g-recaptcha-response' => 'correct',
            'firstName' => 'Test',
            'lastName' => 'Er',
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

    private function endpoint(): JoinEndpoint {
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

        return new JoinEndpoint(
            $this->repo,
            $this->createMock(Newsletter::class),
            $captcha
        );
    }
}
