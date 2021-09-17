<?php
namespace BabelSociety;

use PDO;

class ContactRepository {
  use Singleton;

  /** @var PDO */
  private $db;

  public function __construct(PDO $db) {
    $this->db = $db;
  }

  private static function create(): self {
    return new ContactRepository(getDatabase());
  }

  public function exists(string $email): bool {
    $stmt = $this->db->prepare('SELECT EXISTS (SELECT * FROM contacts WHERE email = ?)');
    $stmt->execute([$email]);

    $result = $stmt->fetch(PDO::FETCH_NUM)[0];
    $stmt->closeCursor();

    return $result;
  }

  public function store(Contact $contact): void {
    $stmt = $this->db->prepare(
      'INSERT INTO contacts(firstName, lastName, email, location, description, contribution)'
      .' VALUES (:first, :last, :email, :loc, :desc, :contr)'
    );

    $stmt->execute([
      'first' => $contact->getFirstName(),
      'last' => $contact->getLastName(),
      'email' => $contact->getEmail(),
      'loc' => $contact->getLocation(),
      'desc' => $contact->getDescription(),
      'contr' => $contact->getContribution(),
    ]);
  }
}
