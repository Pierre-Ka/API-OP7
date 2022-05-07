<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[UniqueEntity(fields: ['email'], message: 'Un utilisateur possède dejà cet email !')]
class User
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["list_user", "show_user"])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Client::class, inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private Client $client;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_user", "show_user"])]
    #[Assert\NotBlank(message: 'L\'utilisateur doit avoir un prenom')]
    #[Assert\Length(min: 3, minMessage: 'Le prenom n\'est pas assez long', max: 100, maxMessage: 'Le nom doit être inferieur à 100 caractères')]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_user", "show_user"])]
    #[Assert\NotBlank(message: 'L\'utilisateur doit avoir un nom')]
    #[Assert\Length(min: 2, minMessage: 'Le nom n\'est pas assez long', max: 100, maxMessage: 'Le nom doit être inferieur à 100 caractères')]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(["list_user", "show_user"])]
    #[Assert\NotBlank(message: 'L\'email doit être renseigné')]
    #[Assert\Email(message:'Entrer un email valide')]
    private string $email;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getClient(): Client
    {
        return $this->client;
    }

    public function setClient(?Client $client): void
    {
        $this->client = $client;
    }

    public function getFirstName(): string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): void
    {
        $this->firstName = $firstName;
    }

    public function getLastName(): string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): void
    {
        $this->lastName = $lastName;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function setEmail(string $email): void
    {
        $this->email = $email;
    }
}