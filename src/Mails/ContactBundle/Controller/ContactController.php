<?php

namespace Mails\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\Actor;
use Mails\MailBundle\Form\ActorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;


class ContactController extends Controller
{

    /**
     * Displays a list of all contacts.
     */
    public function showAllInterlocutorAction() 
    {
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On affiche la liste de tous les interlocuteurs
        $listActor = $lister->listAdminActor();

        return $this->render('MailsContactBundle:Contact:show_all_contact.html.twig', array(
            'actors' => $listActor
            ));

    }

    /**
     * Edit a contact.
     *
     * @param integer $id Actor id
     * @param Request $request Incoming request
     */
     public function editInterlocutorAction($id, Request $request)
     {
        $em = $this->getDoctrine()->getManager();

        // On récupère l'id $id de l'interlocuteur
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
        throw new NotFoundHttpException("L'interlocuteur d'id ".$id." n'existe pas.");
        }

        //On crée le formulaire
        $form = $this->createForm(new ActorType(), $actor);

        // Si la requête est en POST
        if ($form->handleRequest($request)->isValid())
        {
           $em->flush();
           $request->getSession()->getFlashBag()->add('success', 'L\'interlocuteur "'.$actor->getName().'" a bien été modifiée.');
           return $this->redirect($this->generateUrl('mails_contact_show_all'));
        }

        // Si la requête est en GET
        return $this->render('MailsContactBundle:Contact:contact_add.html.twig', array(
        'actorForm'   => $form->createView(),
        'title' => 'Modifier un interlocuteur existant',
        ));
    
     }

     /**
     * Delete a contact.
     *
     * @param integer $id Actor id
     * @param Request $request Incoming request
     */
    public function deleteInterlocutorAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'interlocuteur par son id
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        // On récupère tous les courriers envoyés par l'interlocuteur
        $allMailsentByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByActor($id);
        
        // On récupère tous les courriers reçus par l'interlocuteur
        $allMailreceivedByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByActor($id);

        if (null === $actor) {
        throw new NotFoundHttpException("L'interlocuteur d'id ".$id." n'existe pas.");
        }
        
        //On stocke le nom de l'interlocuteur dans une variable tampon
        $tempActorName = $actor->getName();
        
        if((empty($allMailsentByActor)) && (!empty($allMailreceivedByActor)))
        {
            foreach($allMailreceivedByActor as $mailreceivedByActor)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $em->remove($mailreceivedByActor);
            }
            
            //On supprime l'interlocuteur spécifié
            $em->remove($actor);
            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((!empty($allMailsentByActor)) && (empty($allMailreceivedByActor)))
        {
            foreach($allMailsentByActor as $mailsentByActor)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $em->remove($mailsentByActor);
            }
            
            //On supprime l'interlocuteur spécifié
            $em->remove($actor);
            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((empty($allMailsentByActor)) && (empty($allMailreceivedByActor))) 
        {
            //On supprime l'interlocuteur spécifié
            $em->remove($actor);
            //On exécute ces opérations de suppression
            $em->flush();
        }
        else
        {
            foreach($allMailreceivedByActor as $mailreceivedByActor)
            {
                // On supprime tous les courriers reçus par l'interlocuteur spécifié 
                $em->remove($mailreceivedByActor);
            }
            
            foreach($allMailsentByActor as $mailsentByActor)
            {
                // On supprime tous les courriers envoyé par l'interlocuteur spécifié 
                $em->remove($mailsentByActor);
            }
        
            //On supprime l'interlocuteur spécifié
            $em->remove($actor);
        
            //On exécute ces opérations de suppression
            $em->flush();
        }
        
        $request->getSession()->getFlashBag()->add('success', 'L\'interlocuteur "'.$tempActorName.'" ainsi que tous ses courriers ont bien été supprimés.');

        // On détruit la variable tampon
        unset($tempActorName);
        
        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_contact_show_all'));
    }

    /**
     * Add an actor.
     *
     * @param Request $request Incoming request
     * @Security("has_role('ROLE_ADMIN')")
     */
     public function addInterlocutorAction(Request $request) 
     {
        // Création d'un nouvel interlocuteur
        $actor = new Actor();
        
        // Création du formulaire
        $form = $this->createForm(new ActorType(), $actor);

        // Si la requête est en POST
        if($form->handleRequest($request)->isValid()) 
        {
        
            $em = $this->getDoctrine()->getManager();
            $em->persist($actor);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'L\'interlocuteur "'.$actor->getName().'" à bien été enregistré.');

            return $this->redirect($this->generateUrl('mails_contact_show_all'));
        }
        
        // Si la requête est en GET
        return $this->render('MailsContactBundle:Contact:contact_add.html.twig', array(
        'actorForm' => $form->createView(),
        'title' => 'Ajouter un nouvel interlocuteur'
        ));
        
     }

     /**
     * Displays all the mails of the specified contact.
     *
     * @param integer $id Interlocutor id
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function showAllMailInterlocutorAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'interlocuteur par son id
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        // On récupère tous les courriers envoyés par l'interlocuteur
        $allMailsentByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByActorReverse($id);
        
        // On récupère tous les courriers reçus par l'interlocuteur
        $allMailreceivedByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByActorReverse($id);

        if (null === $actor) {
        throw new NotFoundHttpException("L'interlocuteur d'id ".$id." n'existe pas.");
        }

        return $this->render('MailsContactBundle:Contact:all_mails_contact.html.twig', array(
        'actor' => $actor,
        'allMailsentByActor' => $allMailsentByActor,
        'allMailreceivedByActor' => $allMailreceivedByActor,
        ));
    }
}
