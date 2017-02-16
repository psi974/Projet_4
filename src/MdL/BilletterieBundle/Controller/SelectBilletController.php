<?php

// src/MdL/BilletterieBundle/Controller/SelectBilletController.php

namespace MdL\BilletterieBundle\Controller;

use MdL\BilletterieBundle\Entity\Commande;
use MdL\BilletterieBundle\Form\CommandeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class SelectBilletController extends Controller
{
    public function indexAction(Request $request)
    {
        // Formulaire
        $commande = new Commande();
        $form = $this->get('form.factory')->create(CommandeType::class, $commande);

        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid())
        {
            // Récupération des données contenues dans les billets
            $billets = $commande->getBillets();

            // Récupération du service mdl_billetterie.refcommande -> Détermination d'une référence de commande unique
            $refcommande = $this->container->get('mdl_billetterie.refcommande');
            $refcommande = $refcommande->refCommande();
            $commande->setRefCommande($refcommande);

            // Récupération du service mdl_billetterie.calculprix -> Calcul du prix total en fonction de l'age des visiteurs
            $calculprix = $this->container->get('mdl_billetterie.calculprix');
            $prixtotal = $calculprix->calculPrix($billets);
            $commande->setPrixTotal($prixtotal);


            // Pour test
            //$dtvisite = $_POST['mdl_billetteriebundle_commande']['dtVisite'];
            //var_dump($_POST['mdl_billetteriebundle_commande']['dtVisite']);
            //$newdtvisite = \DateTime::createFromFormat('d M Y - H:i' , $dtvisite);
            //var_dump($_POST['mdl_billetteriebundle_commande']);
            //var_dump($newdtvisite);
            //$commande->setDtVisite($newdtvisite);
            //var_dump($commande);
            //var_dump($commande->getBillets());

            $em = $this->getDoctrine()->getManager();
            // Lien Billets Commande
            foreach($commande->getBillets() as $billet){
                $billet->setCommande($commande);
            }
            $em->persist($commande);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commande bien enregistrée.');
            return $this->redirectToRoute('mdl_billetterie_view');
        }

        // Formulaire invalide
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig', array(
            'form' => $form->createView()
        ));
    }
}