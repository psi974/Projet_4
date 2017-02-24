<?php

// src/MdL/BilletterieBundle/Controller/SelectBilletController.php

namespace MdL\BilletterieBundle\Controller;

use MdL\BilletterieBundle\Entity\Commande;
use MdL\BilletterieBundle\Form\CommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SelectBilletController extends Controller
{
    // Formulaire de sélection des billets et création de la commande
    public function indexAction(Request $request)
    {

        // Formulaire
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            // Récupération des données contenues dans les billets
            $billets = $commande->getBillets();

            // Control Nombre de billets par jour
            $dtvisite = $commande->getDtVisite();
            // Count nombre de billet en DB pour la date de visite sélectionnée
            $nbBilletDB = $this->getDoctrine()->getManager()->getRepository('MdLBilletterieBundle:Billet')->countBydtVisite($dtvisite);
            // Service de CTRL du nombre maximum de billet par jour
            $ctrlnbbillet = $this->container->get('mdl_billetterie.ctrlnbbillet');
            $billetrest = $ctrlnbbillet->ctrlnbbillet($nbBilletDB, $billets);
            // Maximum déjà atteind en DB
            if ($billetrest == 'FULL')
            {
                // Message d'erreur - Musée complet pour ce jour
                $request->getSession()->getFlashBag()->add('error', 'Désolé, le musée est complet à cette date');
                return $this->redirectToRoute('mdl_billetterie_view');
            // Nb billet en DB + Nb billet commandé > maximum (Retour =>integer nombre de billet restant disponible)
            }elseif ($billetrest !== 'OK')
            {
                // Message d'erreur - Reste pas assez de billet pour la commande
                $request->getSession()->getFlashBag()->add('error', 'Désolé, il ne reste que '.$billetrest.' billet(s) à cette date');
                return $this->redirectToRoute('mdl_billetterie_view');
            }

            // Récupération du service mdl_billetterie.refcommande -> Détermination d'une référence de commande unique
            $refcommande = $this->container->get('mdl_billetterie.refcommande');
            $refcommande = $refcommande->refCommande();
            $commande->setRefCommande($refcommande);

            // Récupération du service mdl_billetterie.calculprix -> Calcul du prix total en fonction de l'age des visiteurs
            $calculprix = $this->container->get('mdl_billetterie.calculprix');
            $prixtotal = $calculprix->calculPrix($billets);
            $commande->setPrixTotal($prixtotal);

            $em = $this->getDoctrine()->getManager();
            // Lien Billets Commande
            foreach($commande->getBillets() as $billet){
                $billet->setCommande($commande);
            }
            // Enregistrement BDD
            $em->persist($commande);
            $em->flush();

            // Sélection valide -> Redirection vers le récapitulatif de la commande pour paiement
            return $this->redirectToRoute('mdl_billetterie_cmd',array('id' => $commande->getId()));
        }

        // Formulaire invalide
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    // Récapitulatif de la commande
    public function commandeAction($id)
    {
        // Récupération de la commande en cours
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('MdLBilletterieBundle:Commande')->find($id);

        if (null === $commande) {
            throw new NotFoundHttpException("Cette commande n'existe plus");
        }

        // Récupération des billets correspondants à la commande
        $listBillets = $em->getRepository('MdLBilletterieBundle:Billet')->findBy(array('commande' => $commande));

        // Transfert Objets commande et billets vers la view "récapitulative de la commande"
        return $this->render('MdLBilletterieBundle:CmdBillet:index.html.twig', array(
            'commande' => $commande,
            'listBillets' => $listBillets
        ));
    }

    // Paiement de la commande
    public function paiementAction($id)
    {
        // Récupération de la commande en cours
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('MdLBilletterieBundle:Commande')->find($id);

        if (null === $commande) {
            throw new NotFoundHttpException("Cette commande n'existe plus");
        }

        // Récupération des billets correspondants à la commande
        $listBillets = $em->getRepository('MdLBilletterieBundle:Billet')->findBy(array('commande' => $commande));

        \Stripe\Stripe::setApiKey("sk_test_epZLkY6HtvmbJVJtZC7ciAoc");

        $token = $_POST['stripeToken'];
        $emailClient = $_POST['stripeEmail'];
        $prix = (($commande->getPrixTotal())*100);

        // Enregistrement du paiement
        try
        {
            $charge = \Stripe\Charge::create(array(
                "amount" => $prix,
                "currency" => "eur",
                "source" => $token,
                "description" => "Paiement musée du Louvre"
            ));

            // Enregistrement dans la DB de l'email client pour validation commande
            $commande->setEmailClient($emailClient);
            $em->flush();

            // Message de confirmation de commande
            $this->addFlash('success', 'Votre commande a été enregistrée');
            return $this->redirectToRoute('mdl_billetterie_view');
        }
        catch(\Stripe\Error\Card $e)
        {
            // Message d'erreur paiement non-effectué
            $this->addFlash('error', 'Erreur - commande annulée');
            return $this->redirectToRoute('mdl_billetterie_view');
        }
    }
}