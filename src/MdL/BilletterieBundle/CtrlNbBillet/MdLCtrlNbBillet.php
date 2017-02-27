<?php
// src/MdL/BilletterieBundle/CtrlNbBillet/MdLCtrlNbBillet.php
// CTRL du nombre de billet restant sur la journée

namespace MdL\BilletterieBundle\CtrlNbBillet;

class MdLCtrlNbBillet
{
    public function ctrlnbbillet($nbbilletDB, $billets)
    {
        $nbbillet = 0;
        // pour test modifier le nombre de billet maximum
        if ($nbbilletDB >= 1000)
        {
            $billetrest = 'FULL';
        }else
        {
            foreach ($billets as $billet) {
                $nbbillet++;
            }
            // pour test modifier le nombre de billet maximum
            $billetdif = 1000 - ($nbbillet + $nbbilletDB);
            if ($billetdif < 0) {
                $billetrest = abs($billetdif + $nbbillet);
            } else {
                $billetrest = 'OK';
            }
        }
        return $billetrest;
    }
}