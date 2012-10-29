<?php

namespace AW\Bundle\TwitterOAuthBundle;

use Doctrine\ORM\EntityManager;
use Symfony\Component\HttpFoundation\Session\Session;

class Service
{
    private $entityManager;
    private $session;

    public function __construct(EntityManager $entityManager, Session $session)
    {
        $this->entityManager = $entityManager;
        $this->session = $session;
    }

    /**
     * @return AW\Bundle\TwitterOAuthBundle\Entity\User or null
     */
    public function getUserFromSession()
    {
        $idInSession = $this->session->get('twitter_id');
        if (!$idInSession) {
            return null;
        }

        return $this->entityManager
            ->getRepository('AWTwitterOAuthBundle:User')
            ->find($idInSession);
    }
}
