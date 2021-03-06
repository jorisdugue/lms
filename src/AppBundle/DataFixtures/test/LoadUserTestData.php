<?php

namespace AppBundle\DataFixtures\Common;

use AppBundle\Entity\Institut;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;
use AppBundle\Entity\User;

class LoadUserTestData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $tabInst = [$this->getReference('inst_6'),
        $this->getReference('inst_7'),
        $this->getReference('inst_8'),
        $this->getReference('inst_9')];

        $this->boucleTypeUser($manager, "etudiant", 10, $tabInst);
        $this->boucleTypeUser($manager, "stagiaire", 4, $tabInst);
        $this->boucleTypeUser($manager, "enseignant", 4, $tabInst);

        $manager->flush();
    }

    public function boucleTypeUser(ObjectManager $manager, $intituleUser, $nbUser, $tabInst){
        $prenoms = array('Luc', 'Thomas', 'Damien', 'Antoine', 'David', 'Sylvain');
        $noms = array('Dupont', 'Lefebvre', 'Durand', 'Martineau');
        for($i=0; $i<$nbUser; $i++){
            $randPrenom = $prenoms[mt_rand(0, count($prenoms)-1)];
            $randNom = $noms[mt_rand(0, count($noms)-1)];
            $index = mt_rand(0, count($tabInst)-1);
            $insti = $tabInst[$index];
            $user = $this->createItem($manager,
                'test',
                $randPrenom,
                $randNom,
                $intituleUser.$i.'@test.com',
                $insti,
                false);
            $this->addReference('user_'.$intituleUser . '_'.$i, $user);
        }
    }

    public function createItem(ObjectManager $manager, $password, $firstname, $lastname, $email, Institut $institut, $isSuperAdmin){
        $item = new User();
        $item->setUsername($email);
        $item->setPlainPassword($password);
        $item->setFirstname($firstname);
        $item->setLastname($lastname);
        $item->setEmail($email);
        $item->setInstitut($institut);
        $item->setEnabled(true);
        $item->setSuperAdmin($isSuperAdmin);
        $manager->persist($item);

        return $item;
    }

    /**
     * Get the order of this fixture
     *
     * @return integer
     */
    public function getOrder()
    {
        // the order in which fixtures will be loaded
        // the lower the number, the sooner that this fixture is loaded
        return 7;
    }
}