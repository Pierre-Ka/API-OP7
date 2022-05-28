<?php

namespace App\Serializer;

use App\Entity\Product;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class ProductNormalizer implements NormalizerInterface
{
    private $router;
    private $normalizer;
    protected $type;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($product, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($product, $format, $context);

        if($this->type === 'list')
        {
            $pagination = $this->router->generate('product_list', [
//                'page' => 2,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            array_unshift($data, $pagination);
            $data['_link']['self'] = $this->router->generate('product_show', [
                'id' => $product->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
        else
        {
            $data['_link']['list'] = $this->router->generate('product_list', [], UrlGeneratorInterface::ABSOLUTE_URL);
            return $data;
        }
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(is_array($data))
        {
            $this->type = 'list';
        }
        return $data instanceof Product;
    }
}