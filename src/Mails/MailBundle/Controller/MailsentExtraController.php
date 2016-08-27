<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Form\MailMailsentFilterType;
use Mails\MailBundle\Form\MailSentFilterType;


class MailsentExtraController extends Controller
{

     /**
     * Filter mails sent.
     *
     * @param Request $request Incoming request
     */
     public function filterMailsentAction(Request $request)
     {
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailMailsentFilterType(), $mail);
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre administrateur courant.
            $admin = $this->getUser();

            // On récupère le nombre de jours et la reception du courrier envoyé.
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par admin courant
            $allMailsentByFilter = $filter->filtreMailsent($days, $reception, $admin);

            return $this->render('MailsAdminBundle:Admin:mailsent_filter_result.html.twig', array(
            'allMailsentByFilter' => $allMailsentByFilter,
            'mail' => $mail
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
         
     }

     /**
     * filter mails sent according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     */
     public function filterMailsentByUserAction($id, Request $request)
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
        $form = $this->createForm(new MailMailsentFilterType(), $mail);
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère l'id de l'utilisateur spécifié
            $userId = $user->getId();

            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par user
            $allMailsentFilterByUser = $filter->filtreMailsentByUser($days, $reception, $userId);

            return $this->render('MailsAdminBundle:Admin:user_mailsent_filter_result.html.twig', array(
            'allMailsentFilterByUser' => $allMailsentFilterByUser,
            'mail' => $mail,
            'user' => $user
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:user_mailsent_filter.html.twig', array(
        'user' => $user,    
        'form' => $form->createView()
        ));
         
     }

     /**
     * filter mails sent according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     */
     public function filterMailsentByInterlocutorAction($id, Request $request)
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
        $form = $this->createForm(new MailMailsentFilterType(), $mail);
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère l'id de l'interlocuteur spécifié
            $actorId = $actor->getId();

            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par interlocuteur
            $allMailsentFilterByActor = $filter->filtreMailsentByActor($days, $reception, $actorId);

            return $this->render('MailsAdminBundle:Admin:actor_mailsent_filter_result.html.twig', array(
            'allMailsentFilterByActor' => $allMailsentFilterByActor,
            'mail' => $mail,
            'actor' => $actor
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:actor_mailsent_filter.html.twig', array(
        'actor' => $actor,    
        'form' => $form->createView()
        ));
         
     }

     /**
     * filter all mails sent.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     */
     public function filterAllMailsentAction(Request $request, $page)
     {
         if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
         
        //On crée le mail
        $mail = new Mail();
        
        //On crée notre formulaire
        $form = $this->createForm(new MailSentFilterType(), $mail);
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère les données du courrier envoyé
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            $destinataire = $mail->getMailsent()->getActor()->getName();
            $expediteur = $this->getUser()->getUsername();
            
            // On récupère notre service
            $filter = $this->get('mails_admin.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date et par reception
            $allMailsentFilter = $filter->filtreAllMailsent($days, $reception, $expediteur, $destinataire, $page, $filter::NUM_ITEMS);

            // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
            $nombreTotalMailsSent = $allMailsentFilter->count();
            $nombreMailsentPage = $filter::NUM_ITEMS;
            $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
        
            if($page > $nombreTotalPages){
                throw $this->createNotFoundException("Aucune donnée ne correspond a cette recherche !");
            }

            return $this->render('MailsAdminBundle:Admin:all_mailsent_filter_result.html.twig', array(
            'allMailsentFilter' => $allMailsentFilter,
            'mail' => $mail,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));

        }
        
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsAdminBundle:Admin:all_mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
         
     }

     /**
     * validate a mail sent.
     *
     * @param integer $id Mail sent id
     * @param Request $request Incoming request
     */
     public function validateMailsentAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail sent 
        $mailsent = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

        if (null === $mailsent) {
        throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
        }
        
        //On valide le mail sent
        $mailsent->setValidated(true);
        
        // Inutile de persister ici, Doctrine connait déja notre courrier envoyé
        $em->flush();
       
        //On redirige vers la page d'accueil
        $request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$mailsent->getReference().'" a bien été validé.');

        return $this->redirect($this->generateUrl('mails_core_home'));
                
     }
    
}
