<?php

namespace Mails\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class UserController extends Controller
{
    /**
     * Displays a list of all users
     */
    public function showAllUserAction() 
    {
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On affiche la liste de tous les utilisateurs
        $listUser = $lister->listAdminUser();

        return $this->render('MailsUserBundle:User:user.html.twig', array(
            'users' => $listUser
            ));
    }

    /**
     * Displays a list of all mail sent by the responsible power
     * @param interger $page page number
     */
    public function showAllMailsentCurrentUserAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
    
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère notre service calculator
        $nbPageCalculator = $this->get('mails_mail.nbpage_calculator');

        // On récupère la liste de tous les courriers envoyés par l'administrateur courant
        $listMailsSent = $lister->listAdminMailsent($page, $lister::NUM_ITEMS, $this->getUser());

        // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
        $nombreTotalPages= $nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);

        return $this->render('MailsUserBundle:User:user_mailsent.html.twig', array(
            'mailsSentByActor' => $listMailsSent,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));
    }

    /**
     * Displays a list of all mail received by the responsible power
     * @param interger $page page number
     */
    public function showAllMailreceivedCurrentUserAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère la liste de tous les courriers reçus par l'administrateur courant
        $listMailsReceived = $lister->listAdminMailreceived($page, $lister::NUM_ITEMS, $this->getUser());

        $nombreTotalMailsReceived = $listMailsReceived->count();
        $nombreMailreceivedPage = $lister::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
        if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            //return $this->redirect($this->generateUrl('mails_core_home'));
        } 

        return $this->render('MailsUserBundle:User:user_mailreceived.html.twig', array(
            'mailsReceivedByActor' => $listMailsReceived,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));
    }

    /**
     * Delete an user.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     */
    public function deleteUserAction($id, Request $request)
    {
        // On récupère l'Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'user par son id
        $user = $em->getRepository('MailsUserBundle:User')->find($id);

        // On récupère tous les courriers envoyés par l'user
        $allMailsentByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByUser($id);
        
        // On récupère tous les courriers reçus par l'user
        $allMailreceivedByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByUser($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }
        
        // On stocke le nom de l'utilisateur dans une variable tampon
        $tempUserName = $user->getUsername();

        // On récupère notre service eraser
        $eraser = $this->get('mails_mail.eraser');

        //SUPRESSION
        $eraser->deleteUserAndAllHisMails($user, $allMailsentByUser, $allMailreceivedByUser);
        
        $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur "'.$tempUserName.'" ainsi que tous ses courriers ont bien été supprimés.');

        // On supprime la variable tampon
        unset($tempUserName);

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_core_home'));
    }

    /**
     * Displays all the mails of the specified user.
     *
     * @param integer $id User id
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAllMailOfUserAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'user par son id
        $user = $em->getRepository('MailsUserBundle:User')->find($id);

        // On récupère tous les courriers envoyés par l'user
        $allMailsentByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByUser($id);
        
        // On récupère tous les courriers reçus par l'user
        $allMailreceivedByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByUser($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }
        
        return $this->render('MailsUserBundle:User:user_mails.html.twig', array(
        'user' => $user,
        'allMailsentByUser' => $allMailsentByUser,
        'allMailreceivedByUser' => $allMailreceivedByUser,
        ));
       
    }
    
}
