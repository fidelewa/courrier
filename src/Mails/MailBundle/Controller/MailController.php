<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\Actor;
use Mails\MailBundle\Form\ActorType;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\MailReceived;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Form\MailReceivedType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mails\MailBundle\Form\MailMailsentAdminType;
use Mails\MailBundle\Form\MailMailreceivedAdminType;
use Mails\MailBundle\Form\MailMailsentSecretaryType;
use Mails\MailBundle\Form\MailMailreceivedSecretaryType;


class MailController extends Controller
{
    /**
     * Displays the list of index of mails sent by user profil
     *
     * @param Integer $page page number
     */
    public function showIndexMailsentByUserAction($page)
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //Utilisateur authentifié ou non
        if($this->getUser() !== null)
        {
            //On récupère les roles de l'user
            $userRoles = $this->getUser()->getRoles();
            
            //En fonction du profil on fait la pagination de l'index des courriers envoyés
            if(in_array("ROLE_SECRETAIRE", $userRoles)) 
            {
                // On récupère l'id de la sécrétaire courante
                $idSecretaire = $this->getUser()->getId();
                
                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator->pageIndexMailsentBySecretary($page, $paginator::NUM_ITEMS, $idSecretaire);
                
                // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
                $nombreTotalMailsSent = $listMailsSent->count();
                $nombreMailsentPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                }
                   
            }
            if(in_array("ROLE_ADMINISTRATEUR", $userRoles)) 
            {
                // On récupère l'admin courant
                $admin = $this->getUser();

                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator->pageIndexMailsentNotValidated($page, $paginator::NUM_ITEMS, $admin);
                
                // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
                $nombreTotalMailsSent = $listMailsSent->count();
                $nombreMailsentPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                }
                   
            }
            if(in_array("ROLE_SUPER_ADMIN", $userRoles)) 
            {
                //On récupère l'admin courant
                $admin = $this->getUser();

                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator->pageIndexMailsentNotValidated($page, $paginator::NUM_ITEMS, $admin);
                
                // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
                $nombreTotalMailsSent = $listMailsSent->count();
                $nombreMailsentPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                }
                   
            }
        }
        else
        {
            // On récupère notre service paginator
            $paginator = $this->get('mails_mail.mail_paginator');

            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsSent = $paginator->pageIndexMailsent($page, $paginator::NUM_ITEMS);

            // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
            $nombreTotalMailsSent = $listMailsSent->count();
            $nombreMailsentPage = $paginator::NUM_ITEMS;
            $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
            
            if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            }
        }

        return $this->render('MailsMailBundle:Mail:indexMailsent.html.twig', array(
        'mails' => $listMailsSent,
        'nbPages' => $nombreTotalPages,
        'page' => $page,
        ));
            
    }
    
    /**
     * Displays the list of index of mails received by user profil
     *
     * @param Integer $page page number
     */
    public function showIndexMailreceivedByUserAction($page)
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //Utilisateur authentifié ou non
        if($this->getUser() !== null)
        {
            //On récupère les roles de l'user
            $userRoles = $this->getUser()->getRoles();
            
            if(in_array("ROLE_SECRETAIRE", $userRoles)) 
            {
                //On récupère l'id de la sécrétaire courante
                $idSecretaire = $this->getUser()->getId();

                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator->pageIndexMailreceivedBySecretary($page, $paginator::NUM_ITEMS, $idSecretaire);
                
                // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
                $nombreTotalMailsReceived = $listMailsReceived->count();
                $nombreMailreceivedPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                }   
            }
            if(in_array("ROLE_ADMINISTRATEUR", $userRoles)) 
            {
                // On récupère l'admin courant
                $admin = $this->getUser();

                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator->pageIndexMailreceivedNotValidated($page, $paginator::NUM_ITEMS, $admin);
                
                // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
                $nombreTotalMailsReceived = $listMailsReceived->count();
                $nombreMailreceivedPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                } 
                   
            }
            if(in_array("ROLE_SUPER_ADMIN", $userRoles)) 
            {
                // On récupère l'admin courant
                $admin = $this->getUser();

                // On récupère notre service paginator
                $paginator = $this->get('mails_mail.mail_paginator');

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator->pageIndexMailreceivedNotValidated($page, $paginator::NUM_ITEMS, $admin);
                
                // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
                $nombreTotalMailsReceived = $listMailsReceived->count();
                $nombreMailreceivedPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                } 
                   
            }
        }
        else
        {

            // On récupère notre service paginator
            $paginator = $this->get('mails_mail.mail_paginator');

            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsReceived = $paginator->pageIndexMailreceived($page, $paginator::NUM_ITEMS);

                // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
                $nombreTotalMailsReceived = $listMailsReceived->count();
                $nombreMailreceivedPage = $paginator::NUM_ITEMS;
                $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
                if($page > $nombreTotalPages){
                throw $this->createNotFoundException("La page ".$page." n'existe pas.");
                } 
        }

        return $this->render('MailsMailBundle:Mail:indexMailreceived.html.twig', array(
        'mails' => $listMailsReceived,
        'nombreTotalPages' => $nombreTotalPages,
        'page' => $page,
        ));
    }
    
    /**
     * Displays the list of lastest mails on home page.
     *
     * @param Integer $limit limit number
     */
    public function showLatestMailAction($limit)
    {
        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $latestMailsSent = $indexor->indexLatestMailsent($indexor::NUM_ITEMS);

        $latestMailsReceived = $indexor->indexLatestMailreceived($indexor::NUM_ITEMS);
        
        return $this->render('MailsMailBundle:Mail:listMail.html.twig', array(
            'mailsSent' => $latestMailsSent,
            'mailsReceived' => $latestMailsReceived
        ));
    }
    
    /**
     * Display the list of latest unregistered mails attributed to Secretary
     *
     * @param Integer $limit limit number
     */
    public function showLatestUnregistredMailToSecretaryAction($limit)
    {
        //On récupère l'id de la sécrétaire
        $idSecretaire = $this->getUser()->getId();
        
        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $listMailsentBySecretary = $indexor->indexMailsentNotRegistredBySecretary($idSecretaire, $indexor::NUM_ITEMS);

        $listMailreceivedBySecretary = $indexor->indexMailreceivedNotRegistredBySecretary($idSecretaire, $indexor::NUM_ITEMS);
        
        return $this->render('MailsMailBundle:Mail:listMail_secretary.html.twig', array(
            'listMailsentBySecretary' => $listMailsentBySecretary,
            'listMailreceivedBySecretary' => $listMailreceivedBySecretary,
        ));
    }
    
    /**
     * Display the list of latest not validated mails by user.
     * @param Integer $limit limit number
     */
    public function showLatestNotValidatedMailByUserAction($limit)
    {
        //On récupère notre administrateur courant
        $admin = $this->getUser();

        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $listMailsentByAdmin = $indexor->indexMailsentNotValidatedByAdmin($admin, $indexor::NUM_ITEMS);

        $listMailreceivedByAdmin = $indexor->indexMailreceivedNotValidatedByAdmin($admin, $indexor::NUM_ITEMS);
        
        return $this->render('MailsMailBundle:Mail:listMail_admin.html.twig', array(
            'listMailsentNotValidated' => $listMailsentByAdmin,
            'listMailreceivedNotValidated' => $listMailreceivedByAdmin
        ));
    }

}
