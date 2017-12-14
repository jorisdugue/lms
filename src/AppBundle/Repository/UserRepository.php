<?php

namespace AppBundle\Repository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * UserRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class UserRepository extends \Doctrine\ORM\EntityRepository
{
    public function findByRole($role)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('u')
            ->from($this->_entityName, 'u')
            ->where('u.roles LIKE :roles')
            ->setParameter('roles', '%"'.$role.'"%');

        return $qb->getQuery()->getResult();
    }

    public function getUsers($first_result, $max_results = 10)
    {
        $qb = $this->createQueryBuilder('user');
        $qb
            ->select('user')
            ->addSelect('institut')
            ->leftJoin('user.institut', 'institut')
            ->setFirstResult($first_result)
            ->setMaxResults($max_results);

        $pag = new Paginator($qb);
        return $pag;
    }
}
