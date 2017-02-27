<?php

namespace MdL\BilletterieBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Commande
 *
 * @ORM\Table(name="commande")
 * @ORM\Entity(repositoryClass="MdL\BilletterieBundle\Repository\CommandeRepository")
 */
class Commande
{
    /**
     * @ORM\OneToMany(targetEntity="MdL\BilletterieBundle\Entity\Billet", mappedBy="commande", cascade={"persist"})
     * @Assert\Valid
     */
    private $billets;

    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="refCommande", type="string", length=255, unique=true)
     */
    private $refCommande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtCommande", type="datetime")
     */
    private $dtCommande;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtVisite", type="date")
     */
    private $dtVisite;

    /**
     * @var bool
     *
     * @ORM\Column(name="billetJour", type="boolean")
     */
    private $billetJour = false;

    /**
     * @var int
     *
     * @ORM\Column(name="prixTotal", type="smallint")
     */
    private $prixTotal;

    /**
     * @var string
     *
     * @ORM\Column(name="emailClient", type="string", length=255, nullable=true)
     */
    private $emailClient;


    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $refCommande
     */
    public function setRefCommande($refCommande)
    {
        $this->refCommande = $refCommande;
    }

    /**
     * @return string
     */
    public function getRefCommande()
    {
        return $this->refCommande;
    }

    /**
     * @param \DateTime $dtCommande
     */
    public function setDtCommande($dtCommande)
    {
        $this->dtCommande = $dtCommande;
    }

    /**
     * @return \DateTime
     */
    public function getDtCommande()
    {
        return $this->dtCommande;
    }

    /**
     * @param \DateTime $dtVisite
     */
    public function setDtVisite($dtVisite)
    {
        $this->dtVisite = $dtVisite;
    }

    /**
     * @return \DateTime
     */
    public function getDtVisite()
    {
        return $this->dtVisite;
    }

    /**
     * @param boolean $billetJour
     */
    public function setBilletJour($billetJour)
    {
        $this->billetJour = $billetJour;
    }

    /**
     * @return bool
     */
    public function getBilletJour()
    {
        return $this->billetJour;
    }

    /**
     * @param integer $prixTotal
     */
    public function setPrixTotal($prixTotal)
    {
        $this->prixTotal = $prixTotal;
    }

    /**
     * @return int
     */
    public function getPrixTotal()
    {
        return $this->prixTotal;
    }

    /**
     * @param string $emailClient
     */
    public function setEmailClient($emailClient)
    {
        $this->emailClient = $emailClient;
    }

    /**
     * @return string
     */
    public function getEmailClient()
    {
        return $this->emailClient;
    }

    public function __construct()
    {
        $this->dtCommande = new \Datetime();
        $this->billets = new ArrayCollection();
    }

    /**
     * @param Billet $billet
     */
    public function addBillet(Billet $billet)
    {
        $this->billets[] = $billet;
        $billet->setCommande($this);
    }

    /**
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getBillets()
    {
        return $this->billets;
    }

}

