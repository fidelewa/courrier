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

        return $this->render('MailsAdminBundle:Admin:admin_user.html.twig', array(
            'users' => $listUser
            ));
    }

    /**
     * Displays a list of all mail sent by the responsible power
     * @param interger $page page number
     */
    public function showAllMailsentByUserAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //On récupère l'administrateur courant
        $admin = $this->getUser();

        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère la liste de tous les courriers envoyés par l'administrateur courant
        $listMailsSent = $lister->listAdminMailsent($page, $lister::NUM_ITEMS, $admin);
                
        // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
        $nombreTotalMailsSent = $listMailsSent->count();
        $nombreMailsentPage = $lister::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
                
        if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            //return $this->redirect($this->generateUrl('mails_core_home'));
        }

        return $this->render('MailsAdminBundle:Admin:admin_mailsent.html.twig', array(
            'mailsSentByActor' => $listMailsSent,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));
    }

    /**
     * Displays a list of all mail received by the responsible power
     * @param interger $page page number
     */
    public function showAllMailreceivedByUserAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //On récupère l'administrateur courant
        $admin = $this->getUser();

        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère la liste de tous les courriers reçus par l'administrateur courant
        $listMailsReceived = $lister->listAdminMailreceived($page, $lister::NUM_ITEMS, $admin);
                
        // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
        $nombreTotalMailsReceived = $listMailsReceived->count();
        $nombreMailreceivedPage = $lister::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
        if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            //return $this->redirect($this->generateUrl('mails_core_home'));
        } 

        return $this->render('MailsAdminBundle:Admin:admin_mailreceived.html.twig', array(
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
        
        if((empty($allMailsentByUser)) && (!empty($allMailreceivedByUser)))
        {
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $em->remove($user);
            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((!empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $em->remove($mailsentByUser);
            }

            //On supprime l'user spécifié
            $em->remove($user);

            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            //On supprime l'user spécifié
            $em->remove($user);

            //On exécute ces opérations de suppression
            $em->flush();
        }
        else
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $em->remove($mailsentByUser);
            }
            
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $em->remove($user);
        
            //On exécute ces opérations de suppression
            $em->flush();
        }
        
        $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur "'.$tempUserName.'" ainsi que tous ses courriers ont bien été supprimés.');

        // On supprime la variable tampon
        unset($tempUserName);

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_user_show_all'));
    }

    /**
     * Displays all the mails of the specified user.
     *
     * @param integer $id User id
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAllMailUserAction($id)
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
        
        return $this->render('MailsMailBundle:Mail:mails_user.html.twig', array(
        'user' => $user,
        'allMailsentByUser' => $allMailsentByUser,
        'allMailreceivedByUser' => $allMailreceivedByUser,
        ));
       
    }
    
}
