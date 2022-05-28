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

    public function normalize($user, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($user, $format, $context);

        if($this->type === 'list')
        {
            $pagination = $this->router->generate('user_list', [
//                'page' => 2,
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $create = $this->router->generate('user_create', [], UrlGeneratorInterface::ABSOLUTE_URL);
            array_unshift($data, $pagination, $create);
            $data['_link']['self'] = $this->router->generate('user_show', [
                'user_id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);
            $data['_link']['delete'] = 'BE CAREFULL, IT WILL DELETE THE USER :'.$this->router->generate('user_delete', [
                'user_id' => $user->getId(),
            ], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
        else
        {
            $data['_link']['list'] = $this->router->generate('user_list', [], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if(is_array($data))
        {
            $this->type = 'list';
        }

        return $data instanceof User;
    }
}