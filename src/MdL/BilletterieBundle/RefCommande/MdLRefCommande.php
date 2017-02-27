<?php
// src/MdL/BilletterieBundle/RefCommande/MdLRefCommande.php
// Création d'une référence de commande unique

namespace MdL\BilletterieBundle\RefCommande;

class MdLRefCommande
{
    public function refCommande()
    {
        $refcommande = "MDL".uniqid();
        return $refcommande;
    }
}