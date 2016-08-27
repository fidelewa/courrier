<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Form\MailMailreceivedFilterType;
use Mails\MailBundle\Form\MailReceivedFilterType;


class MailreceivedExtraController extends Controller
{
        /**
     * Filter mails received.
     *
     * @param Request $request Incoming request
     */
     public function filterMailreceivedAction(Request $request)
     {
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), $mail);
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre administrateur courant.
            $admin = $this->getUser();

            // On récupère le nombre de jours, la reception et le traitement du courrier reçu
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            $traitement = $mail->getTreated();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers reçus, filtrés par date, par reception, par traitement et par admin courant
            $allmailreceivedByFilter = $filter->filtreMailreceived($days, $reception, $traitement, $admin);

            return $this->render('MailsAdminBundle:Admin:mailreceived_filter_result.html.twig', array(
            'allmailreceivedByFilter' => $allmailreceivedByFilter,
            'mail' => $mail
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        ));
         
     }
     
     /**
     * filter mails received according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     */
     public function filterMailreceivedByUserAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'user par son id
        $user = $em->getRepository('MailsUserBundle:User')->find($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }
         
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), $mail);
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère l'id de l'utilisateur spécifié
            $userId = $user->getId();

            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            $traitement = $mail->getTreated();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers reçus, filtrés par date, par reception, par user et par traitement
            $allMailreceivedFilterByUser = $filter->filtreMailreceivedByUser($days, $reception, $userId, $traitement);

            return $this->render('MailsAdminBundle:Admin:user_mailreceived_filter_result.html.twig', array(
            'allMailreceivedFilterByUser' => $allMailreceivedFilterByUser,
            'mail' => $mail,
            'user' => $user
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:user_mailreceived_filter.html.twig', array(
        'user' => $user,    
        'form' => $form->createView()
        ));
         
     }
     
     /**
     * filter mails received according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     */
     public function filterMailreceivedByInterlocutorAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'interlocuteur par son id
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
        throw new NotFoundHttpException("L'interlocuteur d'id ".$id." n'existe pas.");
        }
         
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), $mail);
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère l'id de l'interlocuteur spécifié
            $actorId = $actor->getId();

            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            $traitement = $mail->getTreated();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers reçus, filtrés par date, par reception, par interlocuteur et par traitement
            $allMailreceivedFilterByActor = $filter->filtreMailreceivedByActor($days, $reception, $actorId, $traitement);

            return $this->render('MailsAdminBundle:Admin:actor_mailreceived_filter_result.html.twig', array(
            'allMailreceivedFilterByActor' => $allMailreceivedFilterByActor,
            'mail' => $mail,
            'actor' => $actor
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:actor_mailreceived_filter.html.twig', array(
        'actor' => $actor,    
        'form' => $form->createView()
        ));
         
     }

     /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     */
     public function filterAllMailreceivedAction(Request $request, $page)
     {
         if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
         
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailReceivedFilterType(), $mail);
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère les données du courrier reçu
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            $traitement = $mail->getTreated();
            $expediteur = $mail->getMailreceived()->getActor()->getName();
            $destinataire = $this->getUser()->getUsername();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date et par reception
            $allMailreceivedFilter = $filter->filtreAllMailreceived($days, $reception, $expediteur, $destinataire, $traitement, $page, $filter::NUM_ITEMS);

            // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
            $nombreTotalMailsReceived = $allMailreceivedFilter->count();
            $nombreMailreceivedPage = $filter::NUM_ITEMS;
            $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
        
            if($page > $nombreTotalPages){
            throw $this->createNotFoundException("Aucune données ne correspond a cette recherche !");
            }

            return $this->render('MailsAdminBundle:Admin:all_mailreceived_filter_result.html.twig', array(
            'allMailreceivedFilter' => $allMailreceivedFilter,
            'mail' => $mail,
            'nombreTotalPages' => $nombreTotalPages,
            'page' => $page,
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:all_mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        ));
         
     }

     /**
     * validate a mail received.
     *
     * @param integer $id Mail received id
     * @param Request $request Incoming request
     */
     public function validateMailreceivedAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail received 
        $mailreceived = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mailreceived) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }
        
        //On valide le mail received
        $mailreceived->setValidated(true);
        
        // Inutile de persister ici, Doctrine connait déja notre courrier envoyé
        $em->flush();
        
        //On redirige vers la page d'accueil
        $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$mailreceived->getReference().'" a bien été validé.');
        
        return $this->redirect($this->generateUrl('mails_core_home'));
                
     }
    
}
