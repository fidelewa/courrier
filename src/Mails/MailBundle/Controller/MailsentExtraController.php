<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Form\Type\MailMailsentFilterType;
use Mails\MailBundle\Form\Type\MailSentFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

 /**
  * @Security("has_role('ROLE_ADMIN')")
  */
class MailsentExtraController extends Controller
{
     /**
     * Filter mails sent.
     *
     * @param Request $request Incoming request
     */
     public function filterMailsentAction(Request $request)
     {
        //On crée notre formulaire
        $form = $this->createForm(new MailMailsentFilterType(), new Mail());
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {   
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $this->getUser(), 'filtreMailsent');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
     }

     public function filterMailsentResultAction()
     {
        return $this->render('MailsMailBundle:Mail:mailsent_filter_result.html.twig'); 
     }

     /**
     * filter mails sent according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     */
     public function filterMailsentByUserAction($id, Request $request)
     {
        // On récupère l'user par son id
        $user = $this->getDoctrine()->getRepository('MailsUserBundle:User')->find($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        //On crée notre formulaire
        $form = $this->createForm(new MailMailsentFilterType(), new Mail());
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $user, 'filtreMailsentByUser');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_user_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_user_filter.html.twig', array(
        'user' => $user,    
        'form' => $form->createView()
        ));
     }

     public function filterMailsentByUserResultAction()
     {
        return $this->render('MailsMailBundle:Mail:user_mailsent_filter_result.html.twig'); 
     }

     /**
     * filter mails sent according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     */
     public function filterMailsentByInterlocutorAction($id, Request $request)
     {
        // On récupère le contact par son id
        $actor = $this->getDoctrine()->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
        throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        //On crée notre formulaire
        $form = $this->createForm(new MailMailsentFilterType(), new Mail());
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsentData($form->getData(), $actor, 'filtreMailsentByActor');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailsent_filter_actor_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/mailsent_actor_filter.html.twig', array(
        'actor' => $actor,    
        'form' => $form->createView()
        ));
     }

     public function filterMailsentByInterlocutorResultAction()
     {
        return $this->render('MailsMailBundle:Mail:actor_mailsent_filter_result.html.twig'); 
     }

     /**
     * filter all mails sent.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * @Template("@mailsent_filter_result_views/all_mailsent_filter_result.html.twig")
     */
     public function filterAllMailsentAction(Request $request, $page)
     {
         if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //On crée notre formulaire
        $form = $this->createForm(new MailSentFilterType(), new Mail());
         
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
            $filter = $this->get('mails_mail.mail_filter');

            // On récupère notre service calculator
            $nbCalculator = $this->get('mails_mail.nbpage_calculator');

            //On récupère tous les courriers envoyés, filtrés par date et par reception
            $allMailsentFilter = $filter->filtreAllMailsent($days, $reception, $expediteur, $destinataire, $page, $filter::NUM_ITEMS);

            // On calcule le nombre total de pages pour la recherche
            $nombreTotalPagesByFilter = $nbCalculator->calculateTotalNumberPageByFilter($allMailsentFilter, $page, $filter::NUM_ITEMS);

            return array('allMailsentFilter' => $allMailsentFilter, 'mail' => $mail, 'nbPages' => $nombreTotalPagesByFilter, 'page' => $page);
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailsent_form_views/all_mailsent_filter.html.twig', array(
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
