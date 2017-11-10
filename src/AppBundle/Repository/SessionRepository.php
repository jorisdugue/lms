<?php

namespace AppBundle\Repository;

/**
 * SessionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SessionRepository extends \Doctrine\ORM\EntityRepository
{
    public function userIsInscrit($userId, $sessId)
    {
        $em = $this->getEntityManager();
        $session = $this->findOneBy(array('id'=> $sessId));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

        $inscSess = $em->getRepository('AppBundle:Inscription_sess')->findBy(array('session' => $session, 'user' => $user));

        return $inscSess;
    }
}
