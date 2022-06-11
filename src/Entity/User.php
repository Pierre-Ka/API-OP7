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
#[UniqueEntity(fields: ['email'], message: 'This email is already use !')]
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
    #[Assert\NotBlank(message: 'Please, enter a firstname')]
    #[Assert\Length(min: 3, minMessage: 'The firstname is too short!', max: 100, maxMessage: 'The firstname cannot excess 100 chars')]
    private string $firstName;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_user", "show_user"])]
    #[Assert\NotBlank(message: 'Please, enter a lastname')]
    #[Assert\Length(min: 2, minMessage: 'The lastname is too short!', max: 100, maxMessage: 'The lastname cannot excess 100 chars')]
    private string $lastName;

    #[ORM\Column(type: 'string', length: 255, unique: true)]
    #[Groups(["list_user", "show_user"])]
    #[Assert\NotBlank(message: 'Please, enter an email')]
    #[Assert\Email(message:'Please, enter a valid email')]
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