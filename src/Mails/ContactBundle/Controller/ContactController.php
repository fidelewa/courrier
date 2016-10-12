<?php

namespace Mails\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\Actor;
use Mails\MailBundle\Form\Type\ActorType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Security("has_role('ROLE_ADMIN')")
 */
class ContactController extends Controller
{

    /**
     * Displays a list of all contacts.
     */
    public function showAllInterlocutorAction()
    {
        // On récupère notre service lister
        $lister = $this->get('mails_mail.mail_lister');

        // On affiche la liste de tous les contacts
        $listActor = $lister->listAdminActor();

        return $this->render('MailsContactBundle:Contact:contact.html.twig', array(
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

        // On récupère l'id $id du contact
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        if (null === $actor) {
            throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        //On crée le formulaire
        $form = $this->createForm(new ActorType(), $actor);

        // Si la requête est en POST
        if ($form->handleRequest($request)->isValid()) {
            $em->flush();
            
            $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Le contact "'.$actor->getName().'" a bien été modifiée.');
                    
            return $this->redirect($this->generateUrl('mails_contact_show_all'));
        }

        // Si la requête est en GET
        return $this->render('MailsContactBundle:Contact:contact_add.html.twig', array(
        'actorForm'   => $form->createView(),
        'title' => 'Modifier un contact existant',
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

        // On récupère le contact par son id
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        // On récupère tous les courriers envoyés par le contact
        $allMailsentByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByActor($id);

        // On récupère tous les courriers reçus par le contact
        $allMailreceivedByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByActor($id);

        if (null === $actor) {
            throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        //On stocke le nom du contact dans une variable tampon
        $tempActorName = $actor->getName();

        // On récupère notre service eraser
        $eraser = $this->get('mails_mail.eraser');

        //supression du contact
        $eraser->deleteContactAndAllHisMails($actor, $allMailsentByActor, $allMailreceivedByActor);

        //On affiche le message flash
        $request
            ->getSession()
            ->getFlashBag()
            ->add('success', 'Le contact "'.$tempActorName.'" ainsi que tous ses courriers ont bien été supprimés.');

        // On détruit la variable tampon
        unset($tempActorName);

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_contact_show_all'));
    }

    /**
     * Add an actor.
     *
     * @param Request $request Incoming request
     */
    public function addInterlocutorAction(Request $request)
    {
        // Création d'un nouveau contact
        $actor = new Actor();

        // Création du formulaire
        $form = $this->createForm(new ActorType(), $actor);

        // Si la requête est en POST
        if ($form->handleRequest($request)->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($actor);
            $em->flush();

            $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Le contact "'.$actor->getName().'" à bien été enregistré.');

            return $this->redirect($this->generateUrl('mails_contact_show_all'));
        }

        // Si la requête est en GET
        return $this->render('MailsContactBundle:Contact:contact_add.html.twig', array(
        'actorForm' => $form->createView(),
        'title' => 'Ajouter un nouveau contact'
        ));
    }

     /**
     * Displays all the mails of the specified contact.
     *
     * @param integer $id Interlocutor id
     */
    public function showAllMailInterlocutorAction($id)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le contact par son id
        $actor = $em->getRepository('MailsMailBundle:Actor')->find($id);

        // On récupère tous les courriers envoyés par le contact
        $allMailsentByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByActorReverse($id);

        // On récupère tous les courriers reçus par le contact
        $allMailreceivedByActor = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByActorReverse($id);

        if (null === $actor) {
            throw new NotFoundHttpException("Le contact d'id ".$id." n'existe pas.");
        }

        return $this->render('MailsContactBundle:Contact:contact_mails.html.twig', array(
        'actor' => $actor,
        'allMailsentByActor' => $allMailsentByActor,
        'allMailreceivedByActor' => $allMailreceivedByActor,
        ));
    }
}
