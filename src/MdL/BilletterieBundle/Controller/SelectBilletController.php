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


        if ($request->isMethod('POST'))
        {

            // Pour test
            $dateResa = $_POST['mdl_billetteriebundle_commande']['dtVisite'];
            $commande->setDtVisite($dateResa);

            var_dump($dateResa);
            die();

            $em = $this->getDoctrine()->getManager();
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