<?php

namespace Arek2k\ElasticApmSymfony\Security;

use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;

trait TokenStorageTrait
{
    /**
     * @var TokenStorage
     */
    protected $tokenStorage;

    public function setTokenStorage(TokenStorage $tokenStorage)
    {
        $this->tokenStorage = $tokenStorage;
    }

    public function getUser()
    {
        if (null === $tokenStorage = $this->tokenStorage) {
            return null;
        }

        if (null === $token = $tokenStorage->getToken()) {
            return null;
        }

        if (!\is_object($user = $token->getUser())) {
            // e.g. anonymous authentication
            return null;
        }

        return $user;
    }
}
