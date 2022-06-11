<?php

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\Normalizer\ObjectNormalizer;

class UserNormalizer implements NormalizerInterface
{
    protected $type;
    private $router;
    private $normalizer;

    public function __construct(UrlGeneratorInterface $router, ObjectNormalizer $normalizer)  {
        $this->router = $router;
        $this->normalizer = $normalizer;
    }

    public function normalize($user, string $format = null, array $context = [])
    {
        $data = $this->normalizer->normalize($user, $format, $context);
        if ($this->type === 'list') {
            $data['_links']['self'] = $this->router->generate('user_show', ['user_id' => $user->getId()],
                UrlGeneratorInterface::ABSOLUTE_URL);
            $data['_links']['delete'] = 'BE CAREFULL, IT WILL DELETE THE USER :' . $this->router->generate('user_delete',
                    ['user_id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        } else {
            $data['_links']['delete'] = 'BE CAREFULL, IT WILL DELETE THE USER :' . $this->router->generate('user_delete',
                    ['user_id' => $user->getId()], UrlGeneratorInterface::ABSOLUTE_URL);

            return $data;
        }
    }

    public function supportsNormalization($data, string $format = null, array $context = [])
    {
        if (isset($context["groups"])) {
            if (in_array($context["groups"], ["list_product", "list_user"])) {
                $this->type = 'list';
            }

            return $data instanceof User;
        }

        return false;
    }
}
