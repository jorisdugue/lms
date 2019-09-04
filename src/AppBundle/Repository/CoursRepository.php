<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Inscription_c;

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

    public function findRole($user, $cours)
    {
        $em = $this->getEntityManager();
        $discipline = $cours->getDiscipline();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours, 'user' => $user));
        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline, 'user' => $user));
        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));

        $role = "Etudiant";

        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline)){
                    $role = $inscrCoh->getRole()->getNom();
                }elseif($coh->getCours()->contains($cours)){
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

    public function findInscrits($cours)
    {
        $em = $this->getEntityManager();
        $session = $cours->getSession();
        $discipline = $cours->getDiscipline();

        $users = array();

        $inscrCs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        if($inscrCs){
            foreach($inscrCs as $inscrC){
                $inscrUser = $inscrC->getUser();
                if(!in_array($inscrUser, $users) && $inscrUser->isEnabled()){
                    array_push($users, $inscrUser);
                }
            }
        }

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            foreach($inscrDs as $inscrD){
                $inscrUser = $inscrD->getUser();
                if(!in_array($inscrUser, $users) && $inscrUser->isEnabled()){
                    array_push($users, $inscrUser);
                }
            }
        }

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline)){
                    $cohUser = $inscrCoh->getUser();
                    if(!in_array($cohUser, $users) && $cohUser->isEnabled()){
                        array_push($users, $cohUser);
                    }
                }elseif($coh->getCours()->contains($cours)){
                    $cohUser = $inscrCoh->getUser();
                    if(!in_array($cohUser, $users) && $cohUser->isEnabled()){
                        array_push($users, $cohUser);
                    }
                }

            }
        }

        $userToSend = array();
        $repositoryInscrSess = $em->getRepository('AppBundle:Inscription_sess');
        if($session){
            foreach($users as $user){
                $inscSess = $repositoryInscrSess->findBy(array('session' => $session, 'user' => $user));
                if($inscSess){
                    array_push($userToSend, $user);
                }
            }
        }else{
            $userToSend = $users;
        }

        return $userToSend;
    }

    public function userIsInscrit($user, $cours)
    {
        return $this->getUserInscr($user, $cours)!=null;
    }

    public function getUserInscr($user, $cours){
        $em = $this->getEntityManager();

        $insc = $em->getRepository('AppBundle:Inscription_c')->findOneBy(array('cours' => $cours, 'user' => $user));
        return $insc;
    }

    public function getUsersInscr($cours){
        $em = $this->getEntityManager();
        $inscs = $em->getRepository('AppBundle:Inscription_c')->findBy(array('cours' => $cours));
        $users = [];
        if($inscs){
            /* @var $insc Inscription_c */
            foreach ($inscs as $insc){
                $user = $insc->getUser();
                if(!in_array($insc, $users) && $user->isEnabled()) {
                    array_push($users, $user);
                }
            }
        }
        return $users;
    }

    public function userHasAccess($user, $cours)
    {
        $em = $this->getEntityManager();
        $repositoryDiscipline = $em->getRepository('AppBundle:Discipline');
        $disc = $cours->getDiscipline();

        if($repositoryDiscipline->userIsInscrit($user, $disc)){
            return true;
        }
        if($repositoryDiscipline->userHasAccess($user, $disc)){
            return true;
        }
        return false;
    }

    public function userHasAccessOrIsInscrit($user, $cours)
    {
        if($this->userIsInscrit($user, $cours)){
            return true;
        }
        if($this->userHasAccess($user, $cours)){
            return true;
        }
        return false;
    }

    public function getRole($user, $item)
    {
        $em = $this->getEntityManager();

        $inscr = $em->getRepository('AppBundle:Inscription_c')->findOneBy(array('cours' => $item, 'user' => $user));
        if($inscr){
            return $inscr->getRole();
        }else{
            $discipline = $item->getDiscipline();
            $inscrD = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $discipline, 'user' => $user));
            if($inscrD){
                return $inscrD->getRole();
            }else{
                $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));
                if($inscrCohs){
                    foreach($inscrCohs as $inscrCoh){
                        $coh = $inscrCoh->getCohorte();
                        if($coh->getDisciplines()->contains($discipline)){
                            return $inscrCoh->getRole();
                        }elseif($coh->getCours()->contains($item)){
                            return $inscrCoh->getRole();
                        }
                    }
                }
            }
        }
        return null;
    }

    public function getRoleNoInscr($user, $item)
    {
        $em = $this->getEntityManager();

        $discipline = $item->getDiscipline();
        $inscrD = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $discipline, 'user' => $user));
        if($inscrD){
            return $inscrD->getRole();
        }else{
            $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));
            if($inscrCohs){
                foreach($inscrCohs as $inscrCoh){
                    $coh = $inscrCoh->getCohorte();
                    if($coh->getDisciplines()->contains($discipline)){
                        return $inscrCoh->getRole();
                    }elseif($coh->getCours()->contains($item)){
                        return $inscrCoh->getRole();
                    }
                }
            }
        }
        return null;
    }
}
