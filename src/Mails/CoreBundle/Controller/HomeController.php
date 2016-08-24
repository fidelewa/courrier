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
    
}
