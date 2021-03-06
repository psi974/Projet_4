<?php

namespace MdL\BilletterieBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Billet
 *
 * @ORM\Table(name="billet")
 * @ORM\Entity(repositoryClass="MdL\BilletterieBundle\Repository\BilletRepository")
 */
class Billet
{
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
     * @ORM\Column(name="nom", type="string", length=255)
     * @Assert\Length(min=2, minMessage="Le nom doit contenir au moins {{ limit }} caractères.")
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="prenom", type="string", length=255)
     * @Assert\Length(min=2, minMessage="Le prenom doit contenir au moins {{ limit }} caractères.")
     */
    private $prenom;

    /**
     * @var string
     *
     * @ORM\Column(name="pays", type="string", length=255)
     */
    private $pays;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dtNaissance", type="datetime")
     */
    private $dtNaissance;

    /**
     * @var bool
     *
     * @ORM\Column(name="tarifReduit", type="boolean")
     */
    private $tarifReduit = false;

    /**
     * @ORM\ManyToOne(targetEntity="MdL\BilletterieBundle\Entity\Commande", inversedBy="billets")
     * @ORM\JoinColumn(nullable=false)
     */
    private $commande;

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
     * Set nom
     *
     * @param string $nom
     *
     * @return Billet
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
     * Set prenom
     *
     * @param string $prenom
     *
     * @return Billet
     */
    public function setPrenom($prenom)
    {
        $this->prenom = $prenom;

        return $this;
    }

    /**
     * Get prenom
     *
     * @return string
     */
    public function getPrenom()
    {
        return $this->prenom;
    }

    /**
     * Set pays
     *
     * @param string $pays
     *
     * @return Billet
     */
    public function setPays($pays)
    {
        $this->pays = $pays;

        return $this;
    }

    /**
     * Get pays
     *
     * @return string
     */
    public function getPays()
    {
        return $this->pays;
    }

    /**
     * Set dtNaissance
     *
     * @param \DateTime $dtNaissance
     *
     * @return Billet
     */
    public function setDtNaissance($dtNaissance)
    {
        $this->dtNaissance = $dtNaissance;

        return $this;
    }

    /**
     * Get dtNaissance
     *
     * @return \DateTime
     */
    public function getDtNaissance()
    {
        return $this->dtNaissance;
    }

    /**
     * Set prix
     *
     * @param integer $prix
     *
     * @return Billet
     */
    public function setTarifReduit($tarifReduit)
    {
        $this->tarifReduit = $tarifReduit;

        return $this;
    }

    /**
     * Get prix
     *
     * @return int
     */
    public function getTarifReduit()
    {
        return $this->tarifReduit;
    }

    /**
     * @param Commande $commande
     */
    public function setCommande(Commande $commande)
    {
        $this->commande = $commande;
    }
    /**
     * @return Commande
     */
    public function getCommande()
    {
        return $this->commande;
    }
}

