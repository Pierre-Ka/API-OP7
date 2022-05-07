<?php

namespace App\Entity;

use App\Repository\ProductRepository;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Traits\Timestampable;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ProductRepository::class)]
#[ORM\HasLifecycleCallbacks]
class Product
{
    use Timestampable;

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups(["list_product", "show_product"])]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_product", "show_product"])]
    private string $brand;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_product", "show_product"])]
    private string $model;

    #[ORM\Column(type: 'string', length: 255)]
    #[Groups(["list_product", "show_product"])]
    private string $reference;

    #[ORM\Column(type: 'float', nullable: true)]
    #[Groups(["list_product", "show_product"])]
    private float $price;

    #[ORM\Column(type: 'text', nullable: true)]
    #[Groups("show_product")]
    private string $description;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBrand(): string
    {
        return $this->brand;
    }

    public function setBrand(string $brand): void
    {
        $this->brand = $brand;
    }

    public function getModel(): string
    {
        return $this->model;
    }

    public function setModel(string $model): void
    {
        $this->model = $model;
    }

    public function getReference(): string
    {
        return $this->reference;
    }

    public function setReference(string $reference): void
    {
        $this->reference = $reference;
    }

    public function getPrice(): float
    {
        return $this->price;
    }

    public function setPrice(float $price): void
    {
        $this->price = $price;
    }

    public function getDescription(): string
    {
        return $this->description;
    }

    public function setDescription(string $description): void
    {
        $this->description = $description;
    }
}