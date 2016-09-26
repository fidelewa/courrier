<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Form\Type\MailMailreceivedFilterType;
use Mails\MailBundle\Form\Type\MailReceivedFilterType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

 /**
  * @Security("has_role('ROLE_ADMIN')")
  */
class MailreceivedExtraController extends Controller
{
     /**
     * Filter mails received.
     *
     * @param Request $request Incoming request
     */
     public function filterMailreceivedAction(Request $request)
     {
        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), new Mail());
         
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsData($form->getData(), $this->getUser(), 'filtreMailreceived');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        ));  
     }

     public function filterMailreceivedResultAction()
     {
        return $this->render('MailsMailBundle:Mail:mailreceived_filter_result.html.twig'); 
     }
     
     /**
     * filter mails received according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @Template("@mailreceived_form_views/mailreceived_user_filter.html.twig")
     */
     public function filterMailreceivedByUserAction($id, Request $request)
     {
        // On récupère l'user par son id
        $user = $this->getDoctrine()->getRepository('MailsUserBundle:User')->find($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }

        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), new Mail());
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsData($form->getData(), $user, 'filtreMailreceivedByUser');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_user_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return array('user' => $user, 'form' => $form->createView()); 
     }

     public function filterMailreceivedByUserResultAction()
     {
        return $this->render('MailsMailBundle:Mail:user_mailreceived_filter_result.html.twig'); 
     }
     
     /**
     * filter mails received according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     */
     public function filterMailreceivedByInterlocutorAction($id, Request $request)
     {
        // On récupère le contact par son id
        $actor = $this->getDoctrine()->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
        throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedFilterType(), new Mail());
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère notre service handler mails data
            $handlerMailsData = $this->get('mails_mail.mails_handler_data');

            // On traite les données du courrier
            $handlerMailsData->processMailsData($form->getData(), $actor, 'filtreMailreceivedByActor');

            // On redirige vers la route des résultats
            return $this->redirect($this->generateUrl('mails_mailreceived_filter_actor_result'));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/mailreceived_actor_filter.html.twig', array(
        'actor' => $actor,    
        'form' => $form->createView()
        ));
     }

     public function filterMailreceivedByInterlocutorResultAction()
     {
        return $this->render('MailsMailBundle:Mail:actor_mailreceived_filter_result.html.twig'); 
     }

     /**
     * filter all mails received.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * @Template("@mailreceived_filter_result_views/all_mailreceived_filter_result.html.twig")
     */
     public function filterAllMailreceivedAction(Request $request, $page)
     {
         if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
         
        //On crée notre formulaire
        $form = $this->createForm(new MailReceivedFilterType(), new Mail());
         
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
            
            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            // On récupère notre service calculator
            $nbCalculator = $this->get('mails_mail.nbpage_calculator');

            //On récupère tous les courriers envoyés, filtrés par date et par reception
            $allMailreceivedFilter = $filter->filtreAllMailreceived($days, $reception, $expediteur, $destinataire, $traitement, $page, $filter::NUM_ITEMS);

            // On calcule le nombre total de pages pour la recherche
            $nombreTotalPagesByFilter = $nbCalculator->calculateTotalNumberPageByFilter($allMailreceivedFilter, $page, $filter::NUM_ITEMS);

            // On défini les attributs de session
            //$request->getSession()->set('mail', $mail);
            //$request->getSession()->set('allMailreceivedFilter', $allMailreceivedFilter);
            //$request->getSession()->set('nombreTotalPages', $nombreTotalPagesByFilter);
            //$request->getSession()->set('page', $page);

            // On redirige vers la route des résultats
            //return $this->redirect($this->generateUrl('mails_all_mailreceived_filter_result'));

            return array('allMailreceivedFilter' => $allMailreceivedFilter, 'mail' => $mail, 'nombreTotalPages' => $nombreTotalPagesByFilter,'page' => $page);
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('@mailreceived_form_views/all_mailreceived_filter.html.twig', array(
        'form' => $form->createView()
        )); 
     }

     /*public function filterAllMailreceivedResultAction()
     {
        return $this->render('MailsMailBundle:Mail:all_mailreceived_filter_result.html.twig'); 
     }*/

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
