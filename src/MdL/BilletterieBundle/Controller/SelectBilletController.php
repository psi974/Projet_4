<?php

// src/MdL/BilletterieBundle/Controller/SelectBilletController.php

namespace MdL\BilletterieBundle\Controller;

use Eniams\TicketBundle\Form\TicketType;
use MdL\BilletterieBundle\Entity\SelectBillet;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SelectBilletController extends Controller
{
	public function indexAction()
    {
        return $this->render('MdLBilletterieBundle:SelectBillet:index.html.twig');
    }
}