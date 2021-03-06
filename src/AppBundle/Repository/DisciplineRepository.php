<?php

namespace AppBundle\Repository;
use AppBundle\Entity\Inscription_d;
use AppBundle\Entity\User;

/**
 * DisciplineRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DisciplineRepository extends \Doctrine\ORM\EntityRepository
{

    public function findInscrits($discipline)
    {
        $em = $this->getEntityManager();

        $users = array();

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            /* @var $inscrD Inscription_d */
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
                    $inscrUser = $inscrCoh->getUser();
                    if(!in_array($inscrUser, $users) && $inscrUser->isEnabled()){
                        array_push($users, $inscrUser);
                    }
                }

            }
        }

        return $users;
    }

    public function getCohortes($dis)
    {
        $em = $this->getEntityManager();
        $cohortes = array();

        $cohs = $em->getRepository('AppBundle:Cohorte')->findAll();
        if($cohs){
            foreach($cohs as $coh){

                if($coh->getDisciplines()->contains($dis)){
                    if(!in_array($coh, $cohortes)){
                        array_push($cohortes, $coh);
                    }
                }

            }
        }

        return $cohortes;
    }

    public function userIsInscrit($user, $dis)
    {
        return $this->getUserInscr($user, $dis)!=null;
    }

    public function getUserInscr($user, $dis){
        $em = $this->getEntityManager();

        $insc = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $dis, 'user' => $user));
        return $insc;
    }

    public function userHasAccess($user, $discipline)
    {
        $em = $this->getEntityManager();

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array(
            'user' => $user
        ));
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();
                if($coh->getDisciplines()->contains($discipline)){
                    return true;
                }

            }
        }
        if($discipline->getFreeAccess()){
            return true;
        }

        return false;
    }

    public function getUsersInscr($dis){
        $em = $this->getEntityManager();
        $inscs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $dis));
        $users = [];
        if($inscs){
            /* @var $insc Inscription_d */
            foreach ($inscs as $insc){
                $user = $insc->getUser();
                if(!in_array($insc, $users) && $user->isEnabled()) {
                    array_push($users, $user);
                }
            }
        }
        return $users;
    }

    public function userHasAccessOrIsInscrit($user, $disc)
    {
        if($this->userHasAccess($user, $disc)){
            return true;
        }
        if($this->userIsInscrit($user, $disc)){
            return true;
        }
        return false;
    }

    public function getRole($user, $item)
    {
        $em = $this->getEntityManager();

        $inscr = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $item, 'user' => $user));
        if($inscr){
            return $inscr->getRole();
        }else{
            $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findBy(array('user' => $user));
            if($inscrCohs){
                foreach($inscrCohs as $inscrCoh){
                    $coh = $inscrCoh->getCohorte();
                    if($coh->getDisciplines()->contains($item)){
                        return $inscrCoh->getRole();
                    }
                }
            }
        }
        return null;
    }
}
