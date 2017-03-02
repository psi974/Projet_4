<?php
// src/MdL/BilletterieBundle/CtrlNbBillet/MdLCtrlNbBillet.php
// CTRL du nombre de billet disponible pour une date de visite

namespace MdL\BilletterieBundle\CtrlNbBillet;

use Doctrine\ORM\EntityManagerInterface;

class MdLCtrlNbBillet
{
    // Récupération de Doctrine dans le service
    private $em;
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function ctrlNbBillet($billets, $dtvisite)
    {
        // Count nombre de billet en DB pour la date de visite sélectionnée
        $nbBilletDB = $this->em->getRepository('MdLBilletterieBundle:Billet')->countBydtVisite($dtvisite);
        $nbbillet = 0;

        // pour test modifier le nombre de billet maximum
        if ($nbBilletDB >= 1000)
        {
            $billetrest = 'FULL';
        }else
        {
            foreach ($billets as $billet) {
                $nbbillet++;
            }
            // pour test modifier le nombre de billet maximum
            $billetdif = 1000 - ($nbbillet + $nbBilletDB);
            if ($billetdif < 0) {
                $billetrest = abs($billetdif + $nbbillet);
            } else {
                $billetrest = 'OK';
            }
        }
        return $billetrest;
    }
}