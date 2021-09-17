<?php
namespace BabelSociety;

class Contact {
    /** @var string */
    private $firstName;

    /** @var string */
    private $lastName;

    /** @var string */
    private $email;

    /** @var ?string */
    private $location;

    /** @var ?string */
    private $description;

    /** @var ?string */
    private $contribution;

    /** @var bool */
    private $newsletter;

    function __construct(
        string $firstName,
        string $lastName,
        string $email,
        ?string $location,
        ?string $description,
        ?string $contribution,
        bool $newsletter
    ) {
        $this->firstName = $firstName;
        $this->lastName = $lastName;
        $this->email = $email;
        $this->location = $location;
        $this->description = $description;
        $this->contribution = $contribution;
        $this->newsletter = $newsletter;
    }

    public function getFirstName(): string {
        return $this->firstName;
    }

    public function getLastName(): string {
        return $this->lastName;
    }

    public function getEmail(): string {
        return $this->email;
    }

    public function getLocation(): ?string {
        return $this->location;
    }

    public function getDescription(): ?string {
        return $this->description;
    }

    public function getContribution(): ?string {
        return $this->contribution;
    }

    public function wantsNewsletter(): bool {
        return $this->newsletter;
    }
}
