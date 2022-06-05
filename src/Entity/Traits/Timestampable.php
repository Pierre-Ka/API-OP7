<?php
namespace App\Entity\Traits ;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/*
    Pour pouvoir utiliser ce trait :
    Specifier : use Timestampable; dans la classe voulue
    Declarer le namespace : use App\Entity\Traits\Timestampable;
    Declarer #[ORM\HasLifecycleCallbacks] avant la classe
*/
trait Timestampable
{
    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["show_product", "show_client", "show_user"])]
    private ?\DateTimeInterface $createdAt = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    #[Groups(["show_product", "show_client", "show_user"])]
    private ?\DateTimeInterface $updatedAt = null;

    public function getCreatedAt(): ?\DateTimeInterface
    {
        return $this->createdAt;
    }

//    #[ORM\PrePersist]
//    public function setCreatedAtValue(): void
//    {
//        $this->createdAt = new \DateTime();
//    }
    public function setCreatedAt($createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function getUpdatedAt(): ?\DateTimeInterface
    {
        return $this->updatedAt;
    }

//    #[ORM\PreUpdate]
//    public function setUpdatedAtValue(): void
//    {
//        $this->updatedAt = new \DateTime();
//    }
    public function setUpdatedAt($updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }
}