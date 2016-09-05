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
            // On récupère le nombre de jours et la reception du courrier envoyé.
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par admin courant
            $allMailsentByFilter = $filter->filtreMailsent($days, $reception, $this->getUser());

            return $this->render('MailsMailBundle:Mail:mailsent_filter_result.html.twig', array(
            'allMailsentByFilter' => $allMailsentByFilter,
            'mail' => $mail
            ));
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsMailBundle:Mail:mailsent_filter.html.twig', array(
        'form' => $form->createView()
        ));
         
     }

     /**
     * filter mails sent according to the specified user
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     * @Template("MailsMailBundle:Mail:user_mailsent_filter_result.html.twig")
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
            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            // On récupère notre service filter
            $filter = $this->get('mails_mail.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par user
            $allMailsentFilterByUser = $filter->filtreMailsentByUser($days, $reception, $user->getId());

            return array('allMailsentFilterByUser' => $allMailsentFilterByUser, 'mail' => $mail, 'user' => $user);
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsMailBundle:Mail:mailsent_user_filter.html.twig', array(
        'user' => $user,    
        'form' => $form->createView()
        ));
     }

     /**
     * filter mails sent according to the specified interlocutor
     *
     * @param integer $id Interlocutor id
     * @param Request $request Incoming request
     * @Template("MailsMailBundle:Mail:actor_mailsent_filter_result.html.twig")
     */
     public function filterMailsentByInterlocutorAction($id, Request $request)
     {
        // On récupère l'interlocuteur par son id
        $actor = $this->getDoctrine()->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
        throw new NotFoundHttpException("L'interlocuteur d'id ".$id." n'existe pas.");
        }

        //On crée notre formulaire
        $form = $this->createForm(new MailMailsentFilterType(), new Mail());
        
        //Si la requête est en POST on affiche la liste du resultat de la recherche
        if($form->handleRequest($request)->isValid()) 
        {
            // On récupère les données du courrier
            $mail = $form->getData();
            $days = $mail->getNbDaysBefore();
            $reception = $mail->getReceived();
            
            // On récupère notre service
            $filter = $this->get('mails_mail.mail_filter');

            //On récupère tous les courriers envoyés, filtrés par date, par reception et par interlocuteur
            $allMailsentFilterByActor = $filter->filtreMailsentByActor($days, $reception, $actor->getId());

            return array('allMailsentFilterByActor' => $allMailsentFilterByActor, 'mail' => $mail, 'actor' => $actor);
        }
        //Si la requête est en GET on affiche le formulaire de critère de recherche
        return $this->render('MailsMailBundle:Mail:mailsent_actor_filter.html.twig', array(
        'actor' => $actor,    
        'form' => $form->createView()
        ));
     }

     /**
     * filter all mails sent.
     *
     * @param integer $page page number
     * @param Request $request Incoming request
     * @Template("MailsMailBundle:Mail:all_mailsent_filter_result.html.twig")
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
        return $this->render('MailsMailBundle:Mail:all_mailsent_filter.html.twig', array(
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
