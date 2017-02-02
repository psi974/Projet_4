<?php

// src/MdL/BilletterieBundle/Controller/SelectBilletController.php

namespace MdL\BilletterieBundle\Controller;

use MdL\BilletterieBundle\Form\BilletType;
use MdL\BilletterieBundle\Entity\Billet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class SelectBilletController extends Controller
{
    public function indexAction(Request $request)
    {   $em = $this->getDoctrine()->getManager();
        $lesBillets = $em->getRepository('MdLBilletterieBundle:Billet')->findAll();
        $billet = new Billet();
        $form = $this->createForm(BilletType::class, $billet);
        $form->handleRequest($request);
        if($request->isMethod('POST'))
        {
            $dateResa = $_POST['mdl_billetteriebundle_billet']['dtvisite'];
            $billet->setDtvisite($dateResa);
            var_dump($billet);die();
            $em->persist($billet);
            $em->flush();
        }
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig',
            array('form' => $form->createView(),
                'lesBillets' => $lesBillets
            ));
    }
}