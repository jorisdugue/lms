<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Lien
 *
 * @ORM\Table(name="ress_lien")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\LienRepository")
 */
class Lien extends Ressource
{
    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @ORM\OneToMany(targetEntity="AssocGroupeLiens", mappedBy="lien", cascade={"persist", "remove"})
     */
    private $assocGroupes;

    /**
     * @var TypeLien
     *
     * @ORM\ManyToOne(targetEntity="TypeLien")
     * @ORM\JoinColumn(name="typelien_id", referencedColumnName="id", nullable=true)
     */
    protected $typeLien;

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Lien
     */
    public function setUrl($url)
    {
        $this->url = $url;

        return $this;
    }

    /**
     * Get url
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * Set typeLien
     *
     * @param TypeLien $typeLien
     *
     * @return Lien
     */
    public function setTypeLien($typeLien)
    {
        $this->typeLien = $typeLien;

        return $this;
    }

    /**
     * Get typeLien
     *
     * @return TypeLien
     */
    public function getTypeLien()
    {
        return $this->typeLien;
    }

    public function getType()
    {
        return $this::TYPE_LIEN;
    }
}
