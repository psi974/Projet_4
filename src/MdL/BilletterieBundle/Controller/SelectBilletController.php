<?php

// src/MdL/BilletterieBundle/Controller/SelectBilletController.php

namespace MdL\BilletterieBundle\Controller;

use MdL\BilletterieBundle\Entity\Commande;
use MdL\BilletterieBundle\Form\CommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SelectBilletController extends Controller
{
    //----------------------------------------------------------------
    // Formulaire de sélection des billets et création de la commande
    //----------------------------------------------------------------
    public function indexAction(Request $request)
    {
        // Formulaire
        //------------
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            // Récupération des données contenues dans les billets
            $billets = $commande->getBillets();

            // Récupération de la date de visite sélectionée
            $dtvisite = $commande->getDtVisite();

            // Service de CTRL du nombre maximum de billet par jour
            //------------------------------------------------------
            $ctrlnbbillet = $this->container->get('mdl_billetterie.ctrlnbbillet');
            $billetrest = $ctrlnbbillet->ctrlNbBillet($billets, $dtvisite);

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

            // Service de détermination d'une référence de commande unique
            //-------------------------------------------------------------
            $refcommande = $this->container->get('mdl_billetterie.refcommande');
            $refcommande = $refcommande->refCommande();
            $commande->setRefCommande($refcommande);

            // Service Calcul du prix total en fonction de l'age des visiteurs
            //-----------------------------------------------------------------
            $calculprix = $this->container->get('mdl_billetterie.calculprix');
            $prixtotal = $calculprix->calculPrix($billets);
            // Message total de la commande est nul
            if ($prixtotal == 0) {
                $this->addFlash('error', 'Désolé, nous ne pouvons donner suite à votre commande, contactez-nous.');
                return $this->redirectToRoute('mdl_billetterie_view');
            }
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

    //------------------------------
    // Récapitulatif de la commande
    //------------------------------
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

    //----------------------------------------------
    // Paiement de la commande et envoi des billets
    //----------------------------------------------
    public function paiementAction($id)
    {
        // Récupération de la commande en cours
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('MdLBilletterieBundle:Commande')->find($id);
        if (null === $commande) {
            throw new NotFoundHttpException("Cette commande n'existe plus");
        }

        // Récupération des données client
        $token = $_POST['stripeToken'];
        $emailClient = $_POST['stripeEmail'];
        $prix = (($commande->getPrixTotal())*100); //Prix en centimes

        // Service d'enregistrement du paiement STRIPE
        //---------------------------------------------
        $enregpmt = $this->container->get('mdl_billetterie.enregpmt');
        $retpmt = $enregpmt->enregPmt($token, $prix);

        // Paiement enregistré
        if ($retpmt == 'OK') {
            // Enregistrement dans la DB de l'email client pour validation commande
            $commande->setEmailClient($emailClient);
            $em->flush();

            // Récupération des billets correspondants à la commande
            $listBillets = $em->getRepository('MdLBilletterieBundle:Billet')->findBy(array('commande' => $commande));

            // Service d'envoi des billets par mail
            //--------------------------------------
            $message = \Swift_Message::newInstance()
                ->setSubject("Vos billets d'entrée au musée de Louvre")
                ->setFrom(array('MuseeDuLouvre@gmail.com' => 'Musée du Louvre'))
                ->setTo($emailClient)
                ->setBody(
                    $this->renderView(
                    // app/Resources/views/Emails/billets.html.twig
                        'Emails/billets.html.twig',
                        array(
                            'commande' => $commande,
                            'listBillets' => $listBillets)
                    ),
                    'text/html'
                );
            $envoiMail = $this->get('mailer')->send($message);

            // Envoi effectué
            if ($envoiMail) {
                // Message de confirmation de commande
                $this->addFlash('success', 'Commande validée, vos billets vous ont été envoyés par mail');
                return $this->redirectToRoute('mdl_billetterie_view');
            } else { // Erreur d'envoi
                // Message d'erreur problème d'envoi
                $this->addFlash('error', 'L\'envoi de vos billets n\'a pas abouti, conservez la référence votre commande et contactez-nous');
                // Transfert Objets commande et billets vers la view "récapitulative de la commande"
                return $this->render('MdLBilletterieBundle:CmdBillet:index.html.twig', array(
                    'commande' => $commande,
                    'listBillets' => $listBillets
                ));
            }

        } else { // Erreur de paiement

            // Message d'erreur paiement non-effectué
            $this->addFlash('error', 'Erreur - commande annulée');
            return $this->redirectToRoute('mdl_billetterie_view');
        }
    }
}