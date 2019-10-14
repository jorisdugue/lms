<?php

namespace AppBundle\Repository;

/**
 * DocumentRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class DocumentRepository extends \Doctrine\ORM\EntityRepository
{
    public function getByDisc($disc)
    {
        $qb = $this->createQueryBuilder('c')
            ->where('c.discipline = :disc')
            ->setParameter('disc', $disc);

        return $qb->getQuery()->getResult();
    }


    public function findByDisc($discipline, $user){
        $role = "etu";
        $assocsDisc = $this->getEntityManager()->getRepository('AppBundle:AssocDocDisc')->findBy(array('discipline' => $discipline));

        // on récupère tous les documents liés à la discipline
        $documents = array();
        $documentsImportants = array();
        // documents directement associés à la discipline
        for($i=0; $i<count($assocsDisc); $i++) {
            if($assocsDisc[$i]->getIsImportant()){
                if(!in_array($assocsDisc[$i]->getDocument(), $documentsImportants)){
                    array_push($documentsImportants, $assocsDisc[$i]->getDocument());
                }
            }else{
                if(!in_array($assocsDisc[$i]->getDocument(), $documents)){
                    array_push($documents, $assocsDisc[$i]->getDocument());
                }
            }
        }

        $repoAssocDocInscr = $this->getEntityManager()->getRepository('AppBundle:AssocDocInscr');
        $repoInscription_d = $this->getEntityManager()->getRepository('AppBundle:Inscription_d');

        // documents associés à une inscription à une cohorte (à laquelle le user est inscrite) inscrite à la discipline
        $cohortes = $this->getEntityManager()->getRepository('AppBundle:Cohorte')->findAll();
        if($cohortes){
            $repoInscrCoh = $this->getEntityManager()->getRepository('AppBundle:Inscription_coh');
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline)){
                    if ($user->hasRole('ROLE_SUPER_ADMIN')){
                        $inscrCohs = $repoInscrCoh->findBy(array('cohorte' => $cohorte));
                        if($inscrCohs){
                            foreach($inscrCohs as $inscrCoh){
                                $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrCoh, 'cours' => null));
                                for($i=0; $i<count($assocsInscr); $i++) {
                                    if($assocsInscr[$i]->getIsImportant()){
                                        if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                        }
                                    }else{
                                        if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                            array_push($documents, $assocsInscr[$i]->getDocument());
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $inscrCoh = $repoInscrCoh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                        if($inscrCoh){
                            if($inscrCoh->getRole()->getNom() == "Enseignant"){
                                $role = 'ens';
                            }
                            $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrCoh, 'cours' => null));
                            for($i=0; $i<count($assocsInscr); $i++) {
                                if($assocsInscr[$i]->getIsImportant()){
                                    if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                        array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                    }
                                }else{
                                    if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                        array_push($documents, $assocsInscr[$i]->getDocument());
                                    }
                                }
                            }
                        }
                    }

                }
            }
        }

        // documents associés à une inscription à la discipline (à laquelle le user est inscrite)
        if ($user->hasRole('ROLE_SUPER_ADMIN')){
            $inscrDiss = $repoInscription_d->findBy(array('discipline' => $discipline));
            if($inscrDiss){
                foreach($inscrDiss as $inscrDis){
                    $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrDis, 'cours' => null));
                    for($i=0; $i<count($assocsInscr); $i++) {
                        if($assocsInscr[$i]->getIsImportant()){
                            if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                                array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                            }
                        }else{
                            if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                                array_push($documents, $assocsInscr[$i]->getDocument());
                            }
                        }
                    }
                }
            }
        }else{
            $inscrDis = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $discipline));
            if($inscrDis){
                if($inscrDis->getRole()->getNom() == "Enseignant"){
                    $role = 'ens';
                }
                $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrDis, 'cours' => null));
                for($i=0; $i<count($assocsInscr); $i++) {
                    if($assocsInscr[$i]->getIsImportant()){
                        if(!in_array($assocsInscr[$i]->getDocument(), $documentsImportants)){
                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                        }
                    }else{
                        if(!in_array($assocsInscr[$i]->getDocument(), $documents)){
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }
        return array($documents, $documentsImportants, $role);
    }

    public function findByCours($cours, $user){
        $role = "etu";
        $discipline = $cours->getDiscipline();
        $cohortes = $this->getEntityManager()->getRepository('AppBundle:Cohorte')->findAll();
        $assocsCours = $this->getEntityManager()->getRepository('AppBundle:AssocDocCours')->findBy(array('cours' => $cours));

        // on récupère tous les documents liés au cours
        $documents = array();
        $documentsImportants = array();
        // documents directement associés au cours
        for($i=0; $i<count($assocsCours); $i++) {
            if($assocsCours[$i]->getIsImportant()){
                if(!in_array($assocsCours[$i]->getDocument(), $documentsImportants)){
                    array_push($documentsImportants, $assocsCours[$i]->getDocument());
                }
            }else{
                if(!in_array($assocsCours[$i]->getDocument(), $documents)){
                    array_push($documents, $assocsCours[$i]->getDocument());
                }
            }
        }

        $repoAssocDocInscr = $this->getEntityManager()->getRepository('AppBundle:AssocDocInscr');
        $repoInscription_coh = $this->getEntityManager()->getRepository('AppBundle:Inscription_coh');
        $repoInscription_d = $this->getEntityManager()->getRepository('AppBundle:Inscription_d');
        $repoInscription_c = $this->getEntityManager()->getRepository('AppBundle:Inscription_c');

        // documents associés à une inscription à une cohorte (à laquelle le user est inscrit) inscrite au cours ou à la discipline qui la contient
        if($cohortes){
            foreach($cohortes as $cohorte){
                if($cohorte->getDisciplines()->contains($discipline) || $cohorte->getCours()->contains($cours)){
                    if (!$user->hasRole('ROLE_SUPER_ADMIN')){
                        $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                        if($inscrCoh) {
                            if ($role == 'etu' && $inscrCoh->getRole()->getNom() == "Enseignant") {
                                $role = 'ens';
                            }
                        }
                    }
                    /*if ($user->hasRole('ROLE_SUPER_ADMIN')){
                        $inscrCohs = $repoInscription_coh->findBy(array('cohorte' => $cohorte));
                        if ($inscrCohs) {
                            foreach($inscrCohs as $inscrCoh) {
                                $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrCoh, 'cours' => $cours));
                                for ($i = 0; $i < count($assocsInscr); $i++) {
                                    if($assocsInscr[$i]->getIsImportant()){
                                        if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                        }
                                    }else{
                                        if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                            array_push($documents, $assocsInscr[$i]->getDocument());
                                        }
                                    }
                                }
                            }
                        }
                    }else{
                        $inscrCoh = $repoInscription_coh->findOneBy(array('user' => $user, 'cohorte' => $cohorte));
                        if($inscrCoh){
                            if($role == 'etu' && $inscrCoh->getRole()->getNom() == "Enseignant"){
                                $role = 'ens';
                            }
                            $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrCoh, 'cours' => $cours));
                            for($i=0; $i<count($assocsInscr); $i++) {
                                if($assocsInscr[$i]->getIsImportant()){
                                    if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                        array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                                    }
                                }else{
                                    if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                        array_push($documents, $assocsInscr[$i]->getDocument());
                                    }
                                }
                            }
                        }
                    }*/

                }
            }
        }

        if (!$user->hasRole('ROLE_SUPER_ADMIN')){
            $inscrDis = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $discipline));
            if($inscrDis) {
                if ($role == 'etu' && $inscrDis->getRole()->getNom() == "Enseignant") {
                    $role = 'ens';
                }
            }
            $inscrCours = $repoInscription_c->findOneBy(array('user' => $user, 'cours' => $cours));
            if($inscrCours) {
                if ($role == 'etu' && $inscrCours->getRole()->getNom() == "Enseignant") {
                    $role = 'ens';
                }
            }
        }
        $assocsInscr = $repoAssocDocInscr->findBy(array('cours' => $cours));
        if($assocsInscr){
            for ($i = 0; $i < count($assocsInscr); $i++) {
                if($assocsInscr[$i]->getIsImportant()){
                    if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                        array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                    }
                }else{
                    if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                        array_push($documents, $assocsInscr[$i]->getDocument());
                    }
                }
            }
        }


        // documents associés à une inscription à la discipline contenant le cours (à laquelle le user est inscrite)
        /*if ($user->hasRole('ROLE_SUPER_ADMIN')){
            $inscrDiss = $repoInscription_d->findOneBy(array('discipline' => $discipline));
            if($inscrDiss) {
                foreach ($inscrDiss as $inscrDis) {
                    $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrDis, 'cours' => $cours));
                    for ($i = 0; $i < count($assocsInscr); $i++) {
                        if($assocsInscr[$i]->getIsImportant()){
                            if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                                array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                            }
                        }else{
                            if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                                array_push($documents, $assocsInscr[$i]->getDocument());
                            }
                        }
                    }
                }
            }
        }else{
            $inscrDis = $repoInscription_d->findOneBy(array('user' => $user, 'discipline' => $discipline));
            if($inscrDis){
                if($role == 'etu' && $inscrDis->getRole()->getNom() == "Enseignant"){
                    $role = 'ens';
                }
                $assocsInscr = $repoAssocDocInscr->findBy(array('inscription' => $inscrDis, 'cours' => $cours));
                for($i=0; $i<count($assocsInscr); $i++) {
                    if($assocsInscr[$i]->getIsImportant()){
                        if (!in_array($assocsInscr[$i]->getDocument(), $documentsImportants) && $assocsInscr[$i]->getCours() != null) {
                            array_push($documentsImportants, $assocsInscr[$i]->getDocument());
                        }
                    }else{
                        if (!in_array($assocsInscr[$i]->getDocument(), $documents) && $assocsInscr[$i]->getCours() != null) {
                            array_push($documents, $assocsInscr[$i]->getDocument());
                        }
                    }
                }
            }
        }*/
        return array($documents, $documentsImportants, $role);
    }
}
