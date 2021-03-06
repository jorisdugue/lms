<?php

namespace AppBundle\DataFixtures\ORM;

use AppBundle\Entity\CopieFichier;
use AppBundle\Entity\CorrigeFichier;
use AppBundle\Entity\Devoir;
use AppBundle\Entity\DevoirSujet;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\Persistence\ObjectManager;

class LoadCorrigeFichierData extends AbstractFixture implements OrderedFixtureInterface
{

    public function load(ObjectManager $manager)
    {
        $ress = $this->createItem($manager,
            'www.google.fr',
            'corrige Fichier 1',
            $this->getReference('corrige_copie_dev_cours_alg_1_admin')
        );
        $this->addReference('corrigeFichier_copie_dev_cours_alg_1_admin', $ress);

        $ress = $this->createItem($manager,
            'www.google.fr',
            'corrige Fichier 1',
            $this->getReference('corrige_copie_dev_cours_alg_2_admin')
        );
        $this->addReference('corrigeFichier_copie_dev_cours_alg_2_admin', $ress);

        for($i=1; $i<19; $i++){
            $ress = $this->createItem($manager,
                'www.google.fr',
                'corrige Fichier 1',
                $this->getReference('corrige_copie_dev_cours_alg_2_etu'.$i)
            );
            $this->addReference('corrigeFichier_copie_dev_cours_alg_2_etu'.$i, $ress);
        }

        $manager->flush();
    }

    public function createItem(ObjectManager $manager, $url, $nom, $corrige){
        $item = new CorrigeFichier();
        $item->setUrl($url);
        $item->setCorrige($corrige);
        $item->setNom($nom);
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
        return 17;
    }
}