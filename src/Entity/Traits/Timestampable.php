<?php
namespace App\Entity\Traits ;

use DateTime;
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
    private ?\DateTimeInterface $createDate = null;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private ?\DateTimeInterface $lastUpdate = null;

    public function getCreateDate(): ?\DateTimeInterface
    {
        return $this->createDate;
    }

    #[ORM\PrePersist]
    public function setCreateDateAtValue(): void
    {
        $this->createDate = new \DateTime();
    }
    public function setCreateDate($createDate): void
    {
        $this->createDate = $createDate;
    }

    public function getLastUpdate(): ?\DateTimeInterface
    {
        return $this->lastUpdate;
    }

    #[ORM\PreUpdate]
    public function setLastUpdateAtValue(): void
    {
        $this->lastUpdate = new \DateTime();
    }
    public function setLastUpdate($lastUpdate): void
    {
        $this->lastUpdate = $lastUpdate;
    }
}