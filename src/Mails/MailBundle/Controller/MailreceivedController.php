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

class MailreceivedController extends Controller
{
     /**
     * Add or create a mail received action.
     *
     * @param Request $request Incoming request
     * @Security("has_role('ROLE_ADMIN')")
     */
     public function addMailreceivedAction(Request $request)
     {
        //On récupère l'EntityManager
        $em = $this->getDoctrine()->getManager();
        
        //On crée le mail
        $mail = new Mail();
            
        //On crée le mail received
        $mailreceived = new MailReceived();
        
        //On défini la date de reception du courrier reçu à la date courante
        $mailreceived->setdateReception(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
        
        //On défini le mail received
        $mail->setMailreceived($mailreceived);

        //On crée notre formulaire
        $form = $this->createForm(new MailMailreceivedAdminType(), $mail);
        
        // Si la requête est en POST
        if($form->handleRequest($request)->isValid()) 
        {
            //On récupère l'id de la sécrétaire concerné par le courrier
            $mail = $form->getData();
            $idSecretary = $mail->getMailreceived()->getUser()->getId();
            
            //On récupère l'expéditeur du courrier reçu
            $sender = $mail->getMailreceived()->getActor();
            
            //On défini l'expéditeur du courrier reçu
            $mailreceived->setActor($sender);
            
            //On défini la signature de la sécrétaire
            $mail->setVisaSecretaire($idSecretary);
            
            //On défini le destinataire du courrier reçu
            $recipient = $this->getUser();
            $mailreceived->setUser($recipient);
            
            //On défini le courrier reçu
            $mail->setMailreceived($mailreceived);
            
            //On enregiste le courrier reçu en BDD
            $em->persist($mail);
            $em->flush();

            $request->getSession()->getFlashBag()->add('info', 'Le courrier reçu de référence "'.$mail->getReference().'" à bien été crée.');
            
            return $this->redirect($this->generateUrl('mails_mailreceived_detail', array('id' => $mail->getId())));
        }
        
        // Si la requête est en GET
        return $this->render('MailsMailBundle:Mail:mailreceived_add.html.twig', array(
        'form' => $form->createView(),
        ));
         
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
        if ($form->handleRequest($request)->isValid())
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
        // Cela permet de protéger la suppression d'annonce contre cette faille
        $form = $this->createFormBuilder()->getForm();
        
        if($form->handleRequest($request)->isValid()) {
        // Si la requête est en POST, l'annonce sera supprimée
        
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
        return $this->render('MailsMailBundle:Mail:delete_mailreceived.html.twig', array(
        'mail' => $mail,
        'form'   => $form->createView()
        ));

     }

     /**
     * Register a mail received action.
     *
     * @param Request $request Incoming request
     * @param Integer $id mail received id
     * @Security("has_role('ROLE_SECRETAIRE')")
     */
     public function registerMailreceivedAction($id, Request $request)
     {
        //On récupère notre Entity Manager 
        $em = $this->getDoctrine()->getManager();

        // On récupère l'$id du mail received
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }
        
        //On défini la date d'enregistrement du courrier reçu selon la date courante
        $mail->setdateEdition(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
        
        //On crée le formulaire
        $form = $this->createForm(new MailMailreceivedSecretaryType, $mail);
        
        //Si la réquête est en POST
        if($form->handleRequest($request)->isValid()) 
        {
            //On enregistre le courrier reçu
            $mail->setRegistred(true);
        
            //On enregistre le mail received dans la BDD
            $em->persist($mail);
            $em->flush();

            //On redirige vers la page d'accueil
            $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$mail->getReference().'" a bien été enregistré.');

            return $this->redirect($this->generateUrl('mails_core_home'));
        }
        
        //Si la réquête est en GET
        return $this->render('MailsMailBundle:Mail:mailreceived_registred.html.twig', array(
        'form' => $form->createView(),
        ));
          
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