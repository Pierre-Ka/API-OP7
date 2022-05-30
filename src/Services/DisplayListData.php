<?php

namespace App\Services;

/*
 * The DisplayListData class helps to display the datas with pagination informations
 */

use App\Entity\Product;
use App\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;

class DisplayListData
{
    const NUMBER_OF_ITEMS_PER_PAGE = 10;
    private NormalizerInterface $normalizer;
    private UrlGeneratorInterface $router;

    #[Groups(["list_user", "list_product"])]
    private int $actual_page ;
    #[Groups(["list_user", "list_product"])]
    private int $total_pages ;
    #[Groups(["list_user", "list_product"])]
    private int $total_items ;
    #[Groups(["list_user", "list_product"])]
    private int $items_per_page ;
    private ?Collection $data = null;
    private string $dataType;

    #[Groups(["list_user", "list_product"])]
    private array $_links = [];
    #[Groups(["list_user", "list_product"])]
    private $_embedded = [];

    public function __construct(NormalizerInterface $normalizer, UrlGeneratorInterface $router)
    {
        $this->normalizer = $normalizer;
        $this->router = $router;
    }

    public function create($page, $pageCount, $usersCount, $data)
    {
        $this->items_per_page = self::NUMBER_OF_ITEMS_PER_PAGE;
        $this->actual_page = $page;
        $this->total_pages = $pageCount;
        $this->total_items = $usersCount;
        $this->data = new ArrayCollection();
        $this->fillData($data);
        $this->setEmbedded();
        $this->setLinks();

        return $this;

    }

    public function getActualPage(): int
    {
        return $this->actual_page;
    }

    public function setActualPage(int $actual_page): void
    {
        $this->actual_page = $actual_page;
    }

    public function getTotalPages(): int
    {
        return $this->total_pages;
    }

    public function setTotalPages(int $total_pages): void
    {
        $this->total_pages = $total_pages;
    }

    public function getTotalItems(): int
    {
        return $this->total_items;
    }

    public function setTotalItems(int $total_items): void
    {
        $this->total_items = $total_items;
    }

    public function getItemsPerPage(): int
    {
        return $this->items_per_page;
    }

    public function setItemsPerPage(int $items_per_page): void
    {
        $this->items_per_page = $items_per_page;
    }

    public function getData(): ?Collection
    {
        return $this->data;
    }

    public function setData(?Collection $data): void
    {
        $this->data = $data;
    }

    public function getLinks(): array
    {
        return $this->_links;
    }

    public function setLinks(): void
    {
        if ($this->total_pages>=2)
        {
            if ($this->actual_page === 1)
            {
//                $this->router->generate('product_show', [
//                    'id' => $product->getId(),
//                ], UrlGeneratorInterface::ABSOLUTE_URL);
                $this->router->generate($this->dataType.'_list', [ 'page']);
                $this->_links["next page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page=2';
                $this->_links["last page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page='.$this->total_pages;
            }
            elseif ($this->actual_page === $this->total_pages)
            {
                $this->_links["previous page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page='.($this->total_pages-1);
                $this->_links["first page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page=1';
            }
            else
            {
                $this->_links["next page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page='.($this->actual_page+1);
                $this->_links["previous page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page='.($this->actual_page-1);
                $this->_links["last page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page='.$this->total_pages;
                $this->_links["first page"] = 'https://localhost:8000/api/'.$this->dataType.'s?page=1';
            }
        }
    }

    public function getEmbedded(): array
    {
        return $this->_embedded;
    }

    public function setEmbedded(): void
    {
        $dataArray = $this->normalizer->normalize($this->data, null, ['groups' => 'list_'.$this->dataType]);
        $this->_embedded[] = $dataArray;
    }

    public function fillData(array $data): void
    {
        foreach($data as $object)
        {
            if ($object instanceof User || $object instanceof Product)
            {
                preg_match('$App\\\\Entity\\\\([^/?&#]+)$', get_class($object), $matches);
                $this->dataType = strtolower($matches[1]);
                if (!$this->data->contains($object)) {
                    $this->data[] = $object;
                }
            }
        }
    }

    public function eraseData(): self
    {
        $this->data[] = null;
        return $this;
    }

}