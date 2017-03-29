<?php
// test/MdL/BilletterieBundle/CtrlNbBillet/MdLCtrlNbBilletTest.php
// Test - CTRL du nombre de billet disponible pour une date de visite

namespace Test\MdL\BilletterieBundle\CtrlNbBillet;

use MdL\BilletterieBundle\CtrlNbBillet\MdLCtrlNbBillet;
use MdL\BilletterieBundle\Entity\Commande;
use MdL\BilletterieBundle\Entity\Billet;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;


class MdLCtrlNbBilletTest extends KernelTestCase
{
    // Récupération de Doctrine dans le service
    private $em;

    public function testctrlNbBillet()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();

        // Date de visite du 2017-03-08 avec 4 billets déjà enregistrés dans la base de données
        //Ajout 998 billets => Réponse reste 996 billets disponibles
        $commande = new Commande();
        $commande->setDtVisite(new \DateTime('2017-03-08'));
        $cpt = 0;
        while ($cpt < 998)
        {
            $billet = new Billet();
            $commande->addBillet($billet);
            $cpt++;
        }
        $bill = $commande->getBillets();
        $dtv = $commande->getDtVisite();
        $ctrlnbbillet = new MdLCtrlNbBillet($this->em);
        $result = $ctrlnbbillet->ctrlNbBillet($bill, $dtv);
        $this->assertEquals('996', $result);

        // Date de visite du 2017-03-08 avec 4 billets déjà enregistrés dans la base de données
        //Ajout 996 billets => OK.
        $commande = new Commande();
        $commande->setDtVisite(new \DateTime('2017-03-08'));
        $cpt = 0;
        while ($cpt < 996)
        {
            $billet = new Billet();
            $commande->addBillet($billet);
            $cpt++;
        }
        $bill = $commande->getBillets();
        $dtv = $commande->getDtVisite();
        $ctrlnbbillet = new MdLCtrlNbBillet($this->em);
        $result = $ctrlnbbillet->ctrlNbBillet($bill, $dtv);
        $this->assertEquals('OK', $result);

        // Date de visite du 2017-03-08 avec 4 billets déjà enregistrés dans la base de données
        //Ajout 999 billets => Réponse reste 996 billets disponibles
        $commande = new Commande();
        $commande->setDtVisite(new \DateTime('2017-03-08'));
        $cpt = 0;
        while ($cpt < 999)
        {
            $billet = new Billet();
            $commande->addBillet($billet);
            $cpt++;
        }
        $bill = $commande->getBillets();
        $dtv = $commande->getDtVisite();
        $ctrlnbbillet = new MdLCtrlNbBillet($this->em);
        $result = $ctrlnbbillet->ctrlNbBillet($bill, $dtv);
        $this->assertEquals('996', $result);

        // Date de visite du 2017-03-08 avec 4 billets déjà enregistrés dans la base de données
        //Ajout 1000 billets => "FULL".
        $commande = new Commande();
        $commande->setDtVisite(new \DateTime('2017-03-08'));
        $cpt = 0;
        while ($cpt < 1000)
        {
            $billet = new Billet();
            $commande->addBillet($billet);
            $cpt++;
        }
        $bill = $commande->getBillets();
        $dtv = $commande->getDtVisite();
        $ctrlnbbillet = new MdLCtrlNbBillet($this->em);
        $result = $ctrlnbbillet->ctrlNbBillet($bill, $dtv);
        $this->assertEquals('FULL', $result);

    }
}