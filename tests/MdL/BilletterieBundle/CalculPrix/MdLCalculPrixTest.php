<?php
// tests/MdL/BilletterieBundle/CalculPrix/MdLCalculPrixTest.php
// Test - Calcul du prix total en fonction de l'age des visiteurs

namespace Tests\MdL\BilletterieBundle\CalculPrix;

use MdL\BilletterieBundle\CalculPrix\MdLCalculPrix;
use MdL\BilletterieBundle\Entity\Commande;
use MdL\BilletterieBundle\Entity\Billet;
use PHPUnit\Framework\TestCase;

class MdLCalculPrixTest extends TestCase
{
    public function testCalculPrix()
    {
        // Date de naissance 1981-01-01 / Age -> 36 ans / Prix -> 16 euros
        $commande = new Commande();
        $billet = new Billet();
        $billet->setDtNaissance(new \DateTime('1981-01-01'));
        $commande->addBillet($billet);
        $bill = $commande->getBillets();
        $calculprix = new MdLCalculPrix();
        $result = $calculprix->calculPrix($bill);
        $this->assertEquals(16, $result);

        // Date de naissance 1940-05-01 / Age -> 76 ans / Prix -> 12 euros
        $commande = new Commande();
        $billet = new Billet();
        $billet->setDtNaissance(new \DateTime('1940-05-01'));
        $commande->addBillet($billet);
        $bill = $commande->getBillets();
        $calculprix = new MdLCalculPrix();
        $result = $calculprix->calculPrix($bill);
        $this->assertEquals(12, $result);

        // Date de naissance 2017-01-01 / Age -> 0 ans / Prix -> 0 euros
        $commande = new Commande();
        $billet = new Billet();
        $billet->setDtNaissance(new \DateTime('2017-01-01'));
        $commande->addBillet($billet);
        $bill = $commande->getBillets();
        $calculprix = new MdLCalculPrix();
        $result = $calculprix->calculPrix($bill);
        $this->assertEquals(0, $result);

        // Date de naissance 2010-11-14 / Age -> 6 ans / Prix -> 8 euros
        $commande = new Commande();
        $billet = new Billet();
        $billet->setDtNaissance(new \DateTime('2010-11-14'));
        $calculprix = new MdLCalculPrix();
        $commande->addBillet($billet);
        $bill = $commande->getBillets();
        $result = $calculprix->calculPrix($bill);
        $this->assertEquals(8, $result);

        // Calcul prix total commande = 36 euros
        $commande = new Commande();
        // Date de naissance 1981-01-01 / Age -> 36 ans / Prix -> 16 euros
        $billet = new Billet();
        $billet->setDtNaissance(new \DateTime('1981-01-01'));
        $commande->addBillet($billet);
        // +
        // Date de naissance 1940-05-01 / Age -> 76 ans / Prix -> 12 euros
        $billet1 = new Billet();
        $billet1->setDtNaissance(new \DateTime('1940-05-01'));
        $commande->addBillet($billet1);
        // +
        // Date de naissance 2017-01-01 / Age -> 0 ans / Prix -> 0 euros
        $billet2 = new Billet();
        $billet2->setDtNaissance(new \DateTime('2017-01-01'));
        $commande->addBillet($billet2);
        // +
        // Date de naissance 2010-11-14 / Age -> 6 ans / Prix -> 8 euros
        $billet3 = new Billet();
        $billet3->setDtNaissance(new \DateTime('2010-11-14'));
        $commande->addBillet($billet3);

        $bill = $commande->getBillets();
        $calculprix = new MdLCalculPrix();
        $result = $calculprix->calculPrix($bill);
        $this->assertEquals(36, $result);

    }
}