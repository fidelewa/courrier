<?php

namespace Mails\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Mails\CoreBundle\Form\Type\ContactType;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('MailsCoreBundle:Home:index.html.twig');
    }
    
    public function contactAction(Request $request)
    {
        //On crée notre formulaire de contact
        $form = $this->createForm(new ContactType());

        // Check the method
        if ($form->handleRequest($request)->isValid()) {
            // Bind value with form
            //$form->bindRequest($request);

            $data = $form->getData();

            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($data['subject'])
                ->setFrom($data['email'])
                ->setTo('fiderlet07@gmail.com')
                ->setBody($data['content']);

            $this->get('mailer')->send($message);

            // Launch the message flash
            $request
            ->getSession()
            ->getFlashBag()
            ->add('info', 'Merci de nous contacter, nous répondrons à vos questions dans les plus brefs délais.');

            // On redirige vers l'accueil
            return $this->redirect($this->generateUrl('mails_core_home'));
        }

         // Si la requête est en GET
        return $this->render('MailsCoreBundle:Home:contact.html.twig', array(
        'form'   => $form->createView()
        ));
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
