<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements NormalizerInterface
{
    private $router;
    private $normalizer;
    protected $type;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)
    {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    // Comprendre dans le cas de la liste, la fonction normalize est appelé pour chaque user de la liste !
    public function normalize($user, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($user, $format, $context);
        if($this->type === 'list') /* Si il s'agit de la liste lien vers chaque item */
        {
            $data['_links']['self'] = $this->router->generate('user_show', [
                'user_id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $data['_links']['delete'] = 'BE CAREFULL, IT WILL DELETE THE USER :'.$this->router->generate('user_delete', [
                'user_id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
        else /* Si il s'agit d'un item , lien vers la suppression seulement */
        {
            $data['_links']['delete'] = 'BE CAREFULL, IT WILL DELETE THE USER :'.$this->router->generate('user_delete', [
                    'user_id' => $user->getId(),
                ], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(in_array($context["groups"], ["list_product", "list_user"]))
        {
            $this->type = 'list';
        }
        return $data instanceof User;
    }
}