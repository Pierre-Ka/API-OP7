<?php

namespace App\Security;

use App\Entity\Client;
use App\Entity\User;
use Psr\Container\ContainerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

class UserVoter extends Voter
{
    const VIEW = 'view';
    const DELETE = 'delete';
//    protected $container;
//
//    public function __construct(ContainerInterface $container){
//        $this->container = $container;
//    }

    protected function supports(string $attribute, $subject): bool
    {
        if (!in_array($attribute, [self::VIEW, self::DELETE])) {
            return false;
        }

        if (!$subject instanceof User) {
            return false;
        }

        return true;
    }

    protected function voteOnAttribute(string $attribute, $subject, TokenInterface $token): bool
    {

        $client = $token->getUser();
//        if (!($this->container->has('security.token_storage'))||!($this->container->has('security.token_storage')===null))
//        {
//            return false;
//        }
//
//        $client = $this->container->get('security.token_storage')->getToken()->getUser();

        if (!$client instanceof Client) {
            return false;
        }

        /** @var User $user */
        $user = $subject;

        switch ($attribute) {
            case self::VIEW:
                return $this->canView($user, $client);
            case self::DELETE:
                return $this->canDelete($user, $client);
        }

        throw new \LogicException('This code should not be reached!');
    }

    private function canView(User $user, Client $client): bool
    {
        return $client === $user->getClient();
    }

    private function canDelete(User $user, Client $client): bool
    {
        return $client === $user->getClient();
    }
}
