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

            // Control Nombre de billets par jour
            $dtvisite = $commande->getDtVisite();
            $nbBilletDB = $this->getDoctrine()->getManager()->getRepository('MdLBilletterieBundle:Billet')->countBydtVisite($dtvisite);
            $ctrlnbbillet = $this->container->get('mdl_billetterie.ctrlnbbillet');
            $billetrest = $ctrlnbbillet->ctrlnbbillet($nbBilletDB, $billets);

            if ($billetrest == 'FULL')
            {
                // Message d'erreur - Musée complet pour ce jour
                $request->getSession()->getFlashBag()->add('error', 'Désolé, le musée est complet à cette date');
                return $this->redirectToRoute('mdl_billetterie_view');
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

            // Pour test
            //$dtvisite = $_POST['mdl_billetteriebundle_commande']['dtVisite'];
            //$nwdatevisite = \DateTime::createFromFormat('d-F-Y', $dtvisite);
            //var_dump($_POST['mdl_billetteriebundle_commande']['dtVisite']);
            //var_dump($nwdatevisite);
            //die;
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

            // Message de confirmation de commande enregistrée
            $request->getSession()->getFlashBag()->add('success', 'Votre commande a été enregistrée');
            return $this->redirectToRoute('mdl_billetterie_cmd',array('id' => $commande->getId()));
        }

        // Formulaire invalide
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig', array(
            'form' => $form->createView()
        ));
    }

    public function commandeAction($id)
    {
        // Récuération de la commande en cours
        $em = $this->getDoctrine()->getManager();
        $commande = $em->getRepository('MdLBilletterieBundle:Commande')->find($id);

        if (null === $commande) {
            throw new NotFoundHttpException("Cette commande n'existe plus");
        }

        // Récupération des billets correspondants à la commande
        $listBillets = $em->getRepository('MdLBilletterieBundle:Billet')->findBy(array('commande' => $commande));

        // Le render ne change pas, on passait avant un tableau, maintenant un objet
        return $this->render('MdLBilletterieBundle:CmdBillet:index.html.twig', array(
            'commande' => $commande,
            'listBillets' => $listBillets
        ));
    }
}