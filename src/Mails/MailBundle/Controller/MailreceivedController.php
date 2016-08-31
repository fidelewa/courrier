<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\MailReceived;
use Mails\MailBundle\Entity\Mail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mails\MailBundle\Form\MailMailreceivedAdminType;
use Mails\MailBundle\Form\MailMailreceivedSecretaryType;
use Mails\MailBundle\Form\MailMailreceivedEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;


class MailreceivedController extends Controller
{
     /**
     * Add or create a mail received action.
     *
     * @param Request $request Incoming request
     * @Template("MailsMailBundle:Mail:mailreceived_add.html.twig")
     * @Security("has_role('ROLE_ADMIN')")
     */
     public function addMailreceivedAction(Request $request)
     {
        // On récupère notre service mail creator
        $mailCreator = $this->get('mails_mail.mail_creator');
            
        //On crée le courrier reçu
        $mailreceived = new MailReceived();
        
        //On défini la date de reception du courrier reçu à la date courante
        $mailreceived->setdateReception(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));

        //On crée le courrier
        $courier = new Mail();
        
        //On défini le courrier reçu
        $courier->setMailreceived($mailreceived);

        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedAdminType(), $courier);
        
        // Si la requête est en POST
        if($form->handleRequest($request)->isValid()) 
        {
            // On renvoi le conrrier reçu crée
            $mail = $mailCreator->processCreateMailReceived($form, $mailreceived, $this->getUser());
            
            //Redirection vers les détail du courrier
            return $this->redirect($this->generateUrl('mails_mailreceived_detail', array('id' => $mail->getId())));
        }
        // Si la requête est en GET
        return array('form' => $form->createView());
     }

     /**
     * Edit a mail received.
     *
     * @param integer $id Mail received id
     * @param Request $request Incoming request
     * @Security("has_role('ROLE_ADMIN')")
     */
     public function editMailreceivedAction($id, Request $request)
     {
        $em = $this->getDoctrine()->getManager();

        // On récupère le mail received d'id $id
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }

        //On crée le formulaire
        $form = $this->createForm(new MailMailreceivedEditType(), $mail);

        //Si la requête est en POST
        if($form->handleRequest($request)->isValid())
        {
            $em->flush();
            $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$mail->getReference().'" a bien été modifiée.');

            return $this->redirect($this->generateUrl('mails_user_mailreceived'));
            //return $this->redirect($this->generateUrl('mails_mailreceived_detail', array('id' => $mail->getId())));
        }

        //Si la requête est en GET
        return $this->render('MailsMailBundle:Mail:mailreceived_edit.html.twig', array(
        'form'   => $form->createView(),
        'mail' => $mail 
        ));
    
     }

     /**
     * Delete a mail received.
     *
     * @param integer $id mail received id
     * @param Request $request Incoming request
     * @Security("has_role('ROLE_ADMIN')")
     * @Template("MailsMailBundle:Mail:delete_mailreceived.html.twig")
     */
     public function deleteMailreceivedAction($id, Request $request)
     {
        $em = $this->getDoctrine()->getManager();

        // On récupère le mail received d'id $id
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }

        // On crée un formulaire vide, qui ne contiendra que le champ CSRF
        // Cela permet de protéger la suppression de courrier contre cette faille
        $form = $this->createFormBuilder()->getForm();
        
        // Si la requête est en POST, le courrier sera supprimé
        if($form->handleRequest($request)->isValid()) 
        {
            //On stocke la référence du courrier reçu dans une varable tampon
            $tempMailreceivedRef = $mail->getReference();
        
            // On supprime notre objet $mail dans la base de données
            $em->remove($mail);
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$tempMailreceivedRef.'" a bien été supprimé.');

            //On détruit la variable tampon.
            unset($tempMailreceivedRef);
            
            // Puis on redirige vers l'accueil
            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        // Si la requête est en GET, on affiche une page de confirmation avant de supprimer
        return array('mail' => $mail, 'form' => $form->createView());
     }

     /**
     * Register a mail received action.
     *
     * @param Request $request Incoming request
     * @param Integer $id mail received id
     * @Security("has_role('ROLE_SECRETAIRE')")
     * @Template("MailsMailBundle:Mail:mailreceived_registred.html.twig")
     */
     public function registerMailreceivedAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail received
        $mailReceived = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mailReceived) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }
        
        //On défini la date d'enregistrement du courrier reçu selon la date courante
        $mailReceived->setdateEdition(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
        
        //On crée le formulaire
        $form = $this->createForm(new MailMailreceivedSecretaryType, $mailReceived);
        
        //Si la réquête est en POST
        if($form->handleRequest($request)->isValid()) 
        {
            //On enregistre le courrier reçu
            $mailReceived->setRegistred(true);
        
            //On enregistre le courrier reçu dans la BDD
            $em->persist($mailReceived);
            $em->flush();

            //On redirige vers la page d'accueil
            $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$mailReceived->getReference().'" a bien été enregistré.');

            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        //Si la réquête est en GET
        return array('form' => $form->createView());
     }
    
     /**
     * view the features of the mail received
     *
     * @param Integer $id Mailreceived id
     */
     public function viewMailreceivedAction($id)
     {
        //On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        
        // Pour récupérer un courrier reçus unique 
        $mail = $em
        ->getRepository('MailsMailBundle:Mail')
        ->findMailReceived($id)
        ;

        if (null === $mail) {
        throw $this->createNotFoundException("Le courrier reçu d'id ".$id." n'existe pas.");
        }

        return $this->render('MailsMailBundle:Mail:view_mailreceived.html.twig', array(
        'mail' => $mail
        ));
     }
}
