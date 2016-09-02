<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\MailReceived;
use Mails\MailBundle\Entity\Mail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class MailController extends Controller
{
    /**
     * Displays the list of index of mails sent by user profil
     *
     * @param Integer $page page number
     * @Template("MailsMailBundle:Mail:indexMailsent.html.twig")
     */
    public function showIndexMailsentByUserAction($page)
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        // On récupère notre service paginator
        $paginator = $this->get('mails_mail.mail_paginator');

        // On récupère notre service calculator
        $nbPageCalculator = $this->get('mails_mail.nbpage_calculator');

        //Utilisateur authentifié ou non
        if($this->getUser() !== null)
        {
            //On récupère les roles de l'user
            $userRoles = $this->getUser()->getRoles();

            //En fonction du profil on fait la pagination de l'index des courriers envoyés
            if(in_array("ROLE_SECRETAIRE", $userRoles)) 
            {

                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator->pageIndexMailsentBySecretary($page, $paginator::NUM_ITEMS, $this->getUser()->getId());
                   
            }
            if(in_array("ROLE_ADMINISTRATEUR", $userRoles) || in_array("ROLE_SUPER_ADMIN", $userRoles)) 
            {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator->pageIndexMailsentNotValidated($page, $paginator::NUM_ITEMS, $this->getUser());
            }
                
            // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
            $nombreTotalPages= $nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);

        }
        else
        {
            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsSent = $paginator->pageIndexMailsent($page, $paginator::NUM_ITEMS);

            // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
            $nombreTotalPages= $nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);
        }
        
        return array('mails' => $listMailsSent,'nbPages' => $nombreTotalPages,'page' => $page);
    }
    
    /**
     * Displays the list of index of mails received by user profil
     *
     * @param Integer $page page number
     * @Template("MailsMailBundle:Mail:indexMailreceived.html.twig")
     */
    public function showIndexMailreceivedByUserAction($page)
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // On récupère notre service paginator
        $paginator = $this->get('mails_mail.mail_paginator');

        // On récupère notre service calculator
        $nbPageCalculator = $this->get('mails_mail.nbpage_calculator');
        
        //Utilisateur authentifié ou non
        if($this->getUser() !== null)
        {
            //On récupère les roles de l'user
            $userRoles = $this->getUser()->getRoles();
            
            if(in_array("ROLE_SECRETAIRE", $userRoles)) 
            {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator->pageIndexMailreceivedBySecretary($page, $paginator::NUM_ITEMS, $this->getUser()->getId());
                   
            }
            if(in_array("ROLE_ADMINISTRATEUR", $userRoles) || in_array("ROLE_SUPER_ADMIN", $userRoles)) 
            {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator->pageIndexMailreceivedNotValidated($page, $paginator::NUM_ITEMS, $this->getUser());
                   
            }

            // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
            $nombreTotalPages= $nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);

        }
        else
        {
            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsReceived = $paginator->pageIndexMailreceived($page, $paginator::NUM_ITEMS);

            // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
            $nombreTotalPages= $nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);
        }

        return array('mails' => $listMailsReceived, 'nombreTotalPages' => $nombreTotalPages, 'page' => $page);
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
     * @Security("has_role('ROLE_SECRETAIRE')")
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
     * @Security("has_role('ROLE_ADMIN')")
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
