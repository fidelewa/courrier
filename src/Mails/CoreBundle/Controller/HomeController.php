<?php

namespace Mails\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;


class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('MailsCoreBundle:Home:index.html.twig');
    }
    
    public function contactAction(Request $request)
    {
        $session = $request->getSession();
        $session->getFlashBag()->add('info', 'La page de contact n\'est pas encore disponible, merci de revenir plus tard.');

         return $this->redirect($this->generateUrl('mails_core_home'));
    }

    /**
     * Manage all mails
     * @param String $editRoute the name of the edition route
     * @param String $detailRoute the name of the detail route
     * @param Integer $id id number
     */
    public function manageMailsAction($editRoute, $detailRoute, $deleteRoute, $id, $var, $type)
    {
        return $this->render('MailsCoreBundle:Home:manage_mails.html.twig', array(
            'editRoute' => $editRoute,
            'detailRoute' => $detailRoute,
            'deleteRoute' => $deleteRoute,
            'id' => $id,
            'var' => $var,
            'type' => $type,
        ));
    }
    
}
