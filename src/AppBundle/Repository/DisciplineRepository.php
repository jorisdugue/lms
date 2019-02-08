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

    public function findInscrits($id)
    {
        $em = $this->getEntityManager();
        $discipline = $this->findOneBy(array('id'=> $id));

        $users = array();

        $inscrDs = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $discipline));
        if($inscrDs){
            /* @var $inscrD Inscription_d */
            foreach($inscrDs as $inscrD){
                if(!in_array($inscrD->getUser(), $users) && $inscrD->getUser()->isEnabled()){
                    array_push($users, $inscrD->getUser());
                }
            }
        }

        $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
        if($inscrCohs){
            foreach($inscrCohs as $inscrCoh){
                $coh = $inscrCoh->getCohorte();

                if($coh->getDisciplines()->contains($discipline)){
                    if(!in_array($inscrCoh->getUser(), $users) && $inscrCoh->getUser()->isEnabled()){
                        array_push($users, $inscrCoh->getUser());
                    }
                }

            }
        }

        return $users;
    }

    public function getCohortes($disId)
    {
        $em = $this->getEntityManager();
        $dis = $this->findOneBy(array('id'=> $disId));
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

    public function userIsInscrit($userId, $disId)
    {
        return $this->getUserInscr($userId, $disId)!=null;
    }

    public function getUserInscr($userId, $disId){
        $em = $this->getEntityManager();
        $dis = $this->findOneBy(array('id'=> $disId));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

        $insc = $em->getRepository('AppBundle:Inscription_d')->findBy(array('discipline' => $dis, 'user' => $user));
        return $insc;
    }

    public function userHasAccess($userId, $discId)
    {
        $em = $this->getEntityManager();

        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));
        $discipline = $em->getRepository('AppBundle:Discipline')->findOneBy(array('id' => $discId));

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

        return false;
    }

    public function userHasAccessOrIsInscrit($userId, $discId)
    {
        return $this->userHasAccess($userId, $discId) || $this->userIsInscrit($userId, $discId);
    }

    public function getRole($userId, $id)
    {
        $em = $this->getEntityManager();
        $item = $this->findOneBy(array('id'=> $id));
        $user = $em->getRepository('AppBundle:User')->findOneBy(array('id' => $userId));

        $inscr = $em->getRepository('AppBundle:Inscription_d')->findOneBy(array('discipline' => $item, 'user' => $user));
        if($inscr){
            return $inscr->getRole();
        }else{
            $inscrCohs = $em->getRepository('AppBundle:Inscription_coh')->findAll();
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
