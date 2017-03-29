<?php
// src/MdL/BilletterieBundle/EnregPmt/MdLEnregPmt.php
// Enregistrement du paiement dans STRIPE

namespace MdL\BilletterieBundle\EnregPmt;

class MdLEnregPmt
{
    public function enregPmt($token, $prix)
    {
        \Stripe\Stripe::setApiKey("sk_test_epZLkY6HtvmbJVJtZC7ciAoc");

        // Enregistrement du paiement dans Stripe
        try
        {
            $charge = \Stripe\Charge::create(array(
                "amount" => $prix,
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement musée du Louvre"
            ));
            // Paiement enregistré
            $retpmt = "OK";
        }
        catch(\Stripe\Error\Card $e)
        {
            // Erreur de paiement
            $retpmt = "KO";
        }
        return $retpmt;
    }
}