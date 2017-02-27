<?php
// src/MdL/BilletterieBundle/CalculPrix/MdLCalculPrix.php
// Calcul du prix total en fonction de l'age des visiteurs

namespace MdL\BilletterieBundle\CalculPrix;

class MdLCalculPrix
{
    public function calculPrix($billets)
    {
        $prixtotal = 0;

        foreach ($billets as $billet) {
            $dtNaiss = $billet->getDtNaissance()->format('Y-m-d H:i:s');
            $dtNaiss = new \DateTime($dtNaiss);
            $diffan = $dtNaiss->diff(new \DateTime());
            $age = $diffan->y;
            if ($billet->getTarifReduit() and $age >= 4) {
                $prixtotal += 10;
            } else {
                Switch (true) {
                    case ($age < 4):
                        break;
                    case ($age < 12):
                        $prixtotal += 8;
                        break;
                    case ($age < 60):
                        $prixtotal += 16;
                        break;
                    default:
                        $prixtotal += 12;
                }
            }
        }
        return $prixtotal;
    }
}