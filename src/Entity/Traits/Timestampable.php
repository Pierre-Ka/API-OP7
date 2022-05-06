<?php
namespace App\Entity\Traits ;

use Doctrine\ORM\Mapping as ORM;

/*
    Pour pouvoir utiliser ce trait :
    Specifier : use Timestampable; dans la classe voulue
    Declarer le namespace : use App\Entity\Traits\Timestampable;
    Declarer #[ORM\HasLifecycleCallbacks] avant la classe
*/
trait Timestampable
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $UpdatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

//    #[ORM\PrePersist]
    public function setCreatedAtAtValue(): void
    {
        $this->createdAt = new \DateTime();
    }
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->UpdatedAt;
    }

//    #[ORM\PreUpdate]
    public function setUpdatedAtAtValue(): void
    {
        $this->UpdatedAt = new \DateTime();
    }
    public function setUpdatedAt($UpdatedAt): void
    {
        $this->UpdatedAt = $UpdatedAt;
    }
}