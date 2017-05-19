<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Session
 *
 * @ORM\Table(name="session")
 * @ORM\Entity(repositoryClass="AppBundle\Repository\SessionRepository")
 *
 */
class Session extends Evenement
{
    /**
     * @var string
     *
     * @ORM\Column(name="messageAlerts", type="text", nullable=true)
     */
    protected $messageAlerte;

    /**
     * @var string
     *
     * @ORM\Column(name="imgFilePath", type="text", nullable=true)
     */
    private $imgFilePath;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateDebutAlerte", type="datetime", nullable=true)
     */
    private $dateDebutAlerte;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateFinAlerte", type="datetime", nullable=true)
     */
    private $dateFinAlerte;

    /**
     * Set messageAlerte
     *
     * @param string $messageAlerte
     *
     * @return Session
     */
    public function setMessageAlerte($messageAlerte)
    {
        $this->messageAlerte = $messageAlerte;

        return $this;
    }

    /**
     * Get messageAlerte
     *
     * @return string
     */
    public function getMessageAlerte()
    {
        return $this->messageAlerte;
    }

    /**
     * Set imgFilePath
     *
     * @param string $imgFilePath
     *
     * @return Session
     */
    public function setImgFilePath($imgFilePath)
    {
        $this->imgFilePath = $imgFilePath;

        return $this;
    }

    /**
     * Get imgFilePath
     *
     * @return string
     */
    public function getImgFilePath()
    {
        return $this->imgFilePath;
    }

    /**
     * Set dateDebutAlerte
     *
     * @param \DateTime $dateDebutAlerte
     *
     * @return Session
     */
    public function setDateDebutAlerte($dateDebutAlerte)
    {
        $this->dateDebutAlerte = $dateDebutAlerte;

        return $this;
    }

    /**
     * Get dateDebutAlerte
     *
     * @return \DateTime
     */
    public function getDateDebutAlerte()
    {
        return $this->dateDebutAlerte;
    }

    /**
     * Set dateFinAlerte
     *
     * @param \DateTime $dateFinAlerte
     *
     * @return Session
     */
    public function setDateFinAlerte($dateFinAlerte)
    {
        $this->dateFinAlerte = $dateFinAlerte;

        return $this;
    }

    /**
     * Get dateFinAlerte
     *
     * @return \DateTime
     */
    public function getDateFinAlerte()
    {
        return $this->dateFinAlerte;
    }
}