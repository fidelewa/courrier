<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class MailController extends Controller
{
    /**
     * Displays the list of index of mails sent by user profil
     *
     * @param Integer $page page number
     * @Template("@mailsent_index_views/index_mailsent.html.twig")
     */
    public function showIndexMailsentByUserAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }

        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Process
        $listMailsentUserManager = $listMailUserManager->manageListMailsentByUserRole($page, $this->getUser(), $request);

        // result
        return $listMailsentUserManager;
    }
    
    /**
     * Displays the list of index of mails received by user profil
     *
     * @param Integer $page page number
     * @Template("@mailreceived_index_views/index_mailreceived.html.twig")
     */
    public function showIndexMailreceivedByUserAction($page, Request $request)
    {
        if ($page < 1) {
            throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        // On récupère notre service index user role manager
        $listMailUserManager = $this->get('mails_mail.list_mail_user_manager');

        // Process
        $listMailreceivedUserManager = $listMailUserManager->manageListMailreceivedByUserRole($page, $this->getUser(), $request);

        // result
        return $listMailreceivedUserManager;
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
        $latestMailsSent = $indexor->indexLatestMailsent($limit);

        $latestMailsReceived = $indexor->indexLatestMailreceived($limit);
        
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
     */
    public function showLatestUnregistredMailToSecretaryAction($limit)
    {
        //On récupère l'id de la sécrétaire
        $idSecretaire = $this->getUser()->getId();
        
        // On récupère notre service indexor
        $indexor = $this->get('mails_mail.mail_indexor');

        // On récupère notre objet indexor en fonction des critères spécifiés
        $listMailsentBySecretary = $indexor->indexMailsentNotRegistredBySecretary($idSecretaire, $limit);

        $listMailreceivedBySecretary = $indexor->indexMailreceivedNotRegistredBySecretary($idSecretaire, $limit);
        
        return $this->render('@show_latest_mails_views/listMail_secretary.html.twig', array(
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
        $listMailsentByAdmin = $indexor->indexMailsentNotValidatedByAdmin($admin, $limit);

        $listMailreceivedByAdmin = $indexor->indexMailreceivedNotValidatedByAdmin($admin, $limit);
        
        return $this->render('@show_latest_mails_views/listMail_admin.html.twig', array(
            'listMailsentNotValidated' => $listMailsentByAdmin,
            'listMailreceivedNotValidated' => $listMailreceivedByAdmin
        ));
    }
}
