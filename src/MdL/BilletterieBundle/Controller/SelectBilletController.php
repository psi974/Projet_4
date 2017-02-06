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


        if ($request->isMethod('POST') && $form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();

            // Pour test

            var_dump($commande);die();

            $em->persist($commande);
            $em->flush();

            $request->getSession()->getFlashBag()->add('notice', 'Commande bien enregistrée.');

            return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig', array(
                'form' => $form->createView(),
            ));
        }
        // Formulaire invalide
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig', array(
            'form' => $form->createView(),
        ));
    }
}