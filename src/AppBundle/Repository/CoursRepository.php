<?php

namespace AppBundle\Repository;

/**
 * CoursRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class CoursRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByDisc($disc)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.discipline = :disc')
            ->setParameter('disc', $disc);

        return $qb->getQuery()->getResult();
    }

    public function findRole($userId, $coursId)
    {
        $em = $this->getEntityManager();
        $cours = $this->findOneBy(array('id' => $coursId));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        $discipline = $cours->getDiscipline();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours, 'user' => $user));
        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline, 'user' => $user));
        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));

        $role = "Etudiant";

        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($cours)){
                    $role = $inscrCoh->getRole()->getNom();
                }

            }
        }
        if($role == "Etudiant"){
            if($inscrDs){
                $role = $inscrDs->getRole()->getNom();
            }
        }
        if($role == "Etudiant"){
            if($inscrCs){
                $role = $inscrCs->getRole()->getNom();
            }
        }
        return $role;
    }

    public function findInscrits($id)
    {
        $em = $this->getEntityManager();
        $cours = $this->findOneBy(array('id'=> $id));
        $session = $cours->getSession();
        $discipline = $cours->getDiscipline();

        $users = array();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                if(!in_array($inscrC->getUser(), $users)){
                    array_push($users, $inscrC->getUser());
                }
            }
        }

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users)){
                    array_push($users, $inscrD->getUser());
                }
            }
        }

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline) || $coh->getCours()->contains($cours)){
                    if(!in_array($inscrCoh->getUser(), $users)){
                        array_push($users, $inscrCoh->getUser());
                    }
                }

            }
        }

        $userToSend = array();
        if($session){
            foreach($users as $user){
                $inscSess = $em->getRepository('AppBundle:Inscription_sess')->findBy(array('session' => $session, 'user' => $user));
                if($inscSess){
                    array_push($userToSend, $user);
                }
            }
        }else{
            $userToSend = $users;
        }

        return $userToSend;
    }

    public function userIsInscrit($userId, $coursId)
    {
        $em = $this->getEntityManager();
        $cours = $this->findOneBy(array('id'=> $coursId));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

        $insc = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours, 'user' => $user));

        return $insc!=null;
    }

    public function userHasAccess($userId, $coursId)
    {
        $em = $this->getEntityManager();
        $cours = $em->getRepository('AppBundle:Cours')->findOneBy(array('id' => $coursId));
        $disc = $cours->getDiscipline();

        if($em->getRepository('AppBundle:Discipline')->userHasAccess($userId, $disc->getId()) ||
            $em->getRepository('AppBundle:Discipline')->userIsInscrit($userId, $disc->getId())
        ){
            return true;
        }else{
            return false;
        }
    }

    public function userHasAccessOrIsInscrit($userId, $coursId)
    {
        return $this->userHasAccess($userId, $coursId) || $this->userIsInscrit($userId, $coursId);
    }
}
