<?php

namespace AppBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mp3Podcast
 *
 * @ORM\Table(name="ress_mp3podcast")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\Mp3PodcastRepository")
 */
class Mp3Podcast extends OrderedItem
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", unique=true)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=255)
     */
    protected $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     */
    protected $description;

    /**
     * @var string
     *
     * @ORM\Column(name="url", type="text")
     */
    private $url;

    /**
     * @ORM\ManyToOne(targetEntity="Podcast", inversedBy="mp3s")
     * @ORM\JoinColumn(name="podcast_id", referencedColumnName="id")
     */
    private $podcast;

    /**
     * @var bool
     *
     * @ORM\Column(name="isVisible", type="boolean", nullable=true)
     */
    private $isVisible;

    /**
     * Set url
     *
     * @param string $url
     *
     * @return Mp3Podcast
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
     * Get id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * __toString method
     */
    public function __toString()
    {
        return (string) $this->getNom();
    }

    /**
     * Set nom
     *
     * @param string $nom
     *
     * @return Mp3Podcast
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set description
     *
     * @param string $description
     *
     * @return Mp3Podcast
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get description
     *
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return mixed
     */
    public function getPodcast()
    {
        return $this->podcast;
    }

    /**
     * @param mixed $podcast
     *
     * @return Mp3Podcast
     */
    public function setPodcast($podcast)
    {
        $this->podcast = $podcast;

        return $this;
    }

    /**
     * Set isVisible
     *
     * @param boolean $isVisible
     *
     * @return Mp3Podcast
     */
    public function setIsVisible($isVisible)
    {
        $this->isVisible = $isVisible;

        return $this;
    }

    /**
     * Get isVisible
     *
     * @return bool
     */
    public function getIsVisible()
    {
        return $this->isVisible;
    }
}
