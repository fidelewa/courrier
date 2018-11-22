<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Lister\MailsLister;

class MailController extends Controller
{

    /**
     * Displays the list of index of mails sent by user profil/Affiche la liste des index des mails envoyés par profil d'utilisateur
     *
     * @param Integer $page page number
     * @param Request $request
     * @Template("@mailsent_index_views/index_mailsent.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return mixed
     */
    public function showIndexMailsentAction($page, Request $request)
    {
        // Structure conditionnelle sur le numéro de la page
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Traitement
        $listMailsentUserManager = $listMailUserManager
                                ->manageListMailsent($page, $request, $this->getUser()->getCompany());

        // Résultat
        return $listMailsentUserManager;
    }

    /**
     *
     * Displays the list of index of mails sent by user profil
     *
     * @param Integer $page page number
     * @param Request $request
     * @Template("@mailreceived_index_views/index_mailreceived.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return mixed
     */
    public function showIndexMailreceivedAction($page, Request $request)
    {
        // Structure conditionnelle sur le numéro de la page
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Traitement
        $listMailreceivedUserManager = $listMailUserManager
                                    ->manageListMailreceived($page, $request, $this->getUser()->getCompany());
        // Résultat
        return $listMailreceivedUserManager;
    }

    /**
     * Displays the list of index of mails sent by user profil
     *
     * @param Integer $page page number
     * @param Request $request
     * @Template("@mailsent_index_views/index_mailsent_user.html.twig")
     * @Security("has_role('ROLE_USER')")
     *
     * @return mixed
     */
    public function showIndexMailsentByUserAction($page, Request $request)
    {
        // Structure conditionnelle sur le numéro de la page

        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Traitement
        $listMailsentUserManager = $listMailUserManager
        ->manageListMailsentByUserRole($page, $this->getUser(), $request);

        // Résultat
        return $listMailsentUserManager;
    }

    /**
     * Displays the list of index of mails received by user profil
     *
     * @param Integer $page page number
     * @Template("@mailreceived_index_views/index_mailreceived_user.html.twig")
     * @Security("has_role('ROLE_USER')")
     * @param Request $request
     *
     * @return mixed
     */
    public function showIndexMailreceivedByUserAction($page, Request $request)
    {
        // Structure conditionnelle sur le numéro de la page
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Traitement
        $listMailreceivedUserManager = $listMailUserManager
        ->manageListMailreceivedByUserRole($page, $this->getUser(), $request);

        // Résultat
        return $listMailreceivedUserManager;
    }

    /**
     * Displays the list of lastest mails on home page.
     *
     * @param Integer $limit limit number
     * @param $idCompany
     * @Security("has_role('ROLE_USER')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showLatestMailAction($idCompany, $limit = MailsLister::LIMIT)
    {
        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $latestMailsSent = $indexor->indexLatestMailsent($limit, $idCompany);
        $latestMailsReceived = $indexor->indexLatestMailreceived($limit, $idCompany);
        
        // On retourne la vue correspondante
        return $this->render('@show_latest_mails_views/listMail.html.twig', array(
            'mailsSent' => $latestMailsSent,
            'mailsReceived' => $latestMailsReceived
        ));
    }

    /**
     * Display the list of latest unregistered mails attributed to Secretary
     *
     * @param Integer $limit limit number
     * @Security("has_role('ROLE_SECRETAIRE')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showLatestUnregistredMailToSecretaryAction($limit = MailsLister::LIMIT)
    {
        //On récupère l'id de la sécrétaire
        $idSecretaire = $this->getUser()->getId();
        
        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $listMailsentBySecretary = $indexor->indexMailsentNotRegistredBySecretary($idSecretaire, $limit);
        $listMailreceivedBySecretary = $indexor->indexMailreceivedNotRegistredBySecretary($idSecretaire, $limit);
        
        // On retourne la vue correspondante
        return $this->render('MailsCoreBundle:Home:secretary_workspace.html.twig', array(
            'listMailsentBySecretary' => $listMailsentBySecretary,
            'listMailreceivedBySecretary' => $listMailreceivedBySecretary,
        ));
    }

    /**
     * Display the list of latest not validated mails by user.
     * 
     * @param Integer $limit limit number
     * @Security("has_role('ROLE_ADMIN')")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function showLatestNotValidatedMailByUserAction($limit = MailsLister::LIMIT)
    {
        //On récupère notre administrateur courant
        $admin = $this->getUser();

        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $listMailsentByAdmin = $indexor->indexMailsentNotValidatedByAdmin($admin, $limit);
        $listMailreceivedByAdmin = $indexor->indexMailreceivedNotValidatedByAdmin($admin, $limit);
        
        // On retourne la vue correspondante
        return $this->render('MailsCoreBundle:Home:admin_workspace.html.twig', array(
            'listMailsentNotValidated' => $listMailsentByAdmin,
            'listMailreceivedNotValidated' => $listMailreceivedByAdmin
        ));
    }
}
