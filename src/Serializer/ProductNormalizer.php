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

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($product, string $format = null, array $context = [])
    {
//        dd('ici');
        $data = $this->normalizer->normalize($product, $format, $context);

        // Here, add, edit, or delete some data:
        $data['__link']['self'] = $this->router->generate('product_show', [
            'id' => $product->getId(),
        ], UrlGeneratorInterface::ABSOLUTE_URL);

        return $data;
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
//        dd(is_array($data));
//        dd($data instanceof Product);
//        dump('avant dump');
//        dump($data instanceof Product);
//        dump('apres dump');
//        return $data instanceof Product;
        return false;
    }
}