<?php

namespace Mails\AdminBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Form\MailMailsentEditType;
use Mails\MailBundle\Form\MailMailreceivedEditType;
use Mails\MailBundle\Entity\Mail;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\Actor;
use Mails\MailBundle\Form\ActorType;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Mails\MailBundle\Form\MailMailsentFilterType;
use Mails\MailBundle\Form\MailMailreceivedFilterType;
use Mails\UserBundle\Entity\User;
use Mails\MailBundle\Form\MailSentFilterType;
use Mails\MailBundle\Form\MailReceivedFilterType;

class AdminController extends Controller
{   
       
    /**
     * Edit a mail sent.
     *
     * @param integer $id Mail sent id
     * @param Request $request Incoming request
     */
    public function editMailsentAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le mail sent d'id $id
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
        }
        
        //On récupère les attributs du mailsent existant en BDD
        $id = $mail->getMailsent()->getId(); 
        $actor = $mail->getMailsent()->getActor(); 
        $user = $mail->getMailsent()->getUser(); 
        $dateEnvoi = $mail->getMailsent()->getDateEnvoi();
        
        //On instancie un nouveau mail sent 
        $mailsent = new MailSent();
        
        //On met a jour ses attributs
        $mailsent->setId($id);
        $mailsent->setActor($actor);
        $mailsent->setUser($user);
        $mailsent->setDateEnvoi($dateEnvoi);

        //On défini le mail sent
        $mail->setMailsent($mailsent);

        //On crée le formulaire
        $form = $this->createForm(new MailMailsentEditType(), $mail);

        //Si la requête est en POST 
        if($form->handleRequest($request)->isValid()) 
        {
            // Inutile de persister ici, Doctrine connait déja notre courrier envoyé
            $em->flush();

            $request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$mail->getReference().'" a bien été modifiée.');

            return $this->redirect($this->generateUrl('mails_admin_mailsent'));
    
        }

        //Si la requête est en GET
        return $this->render('MailsAdminBundle:Admin:mailsent_edit.html.twig', array(
        'form'   => $form->createView(),
        'mail' => $mail // Je passe également le courrier envoyé a la vue si jamais elle veut l'afficher
        ));
    }

    /**
     * Delete a mail sent.
     *
     * @param integer $id mail sent id
     * @param Request $request Incoming request
     */
    public function deleteMailsentAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le mail sent d'id $id
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
        }
        
        //On stocke la référence du courrier envoyé dans une varable tampon
        $tempMailsentRef = $mail->getReference();
        
        // On supprime notre objet $mail dans la base de données
        $em->remove($mail);
        $em->flush();
        
        $request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$tempMailsentRef.'" a bien été supprimé.');
        
        //On détruit la variable tampon.
        unset($tempMailsentRef);

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_admin_mailsent'));
    }

    /**
     * Edit a mail received.
     *
     * @param integer $id Mail received id
     * @param Request $request Incoming request
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

            return $this->redirect($this->generateUrl('mails_admin_mailreceived'));
            //return $this->redirect($this->generateUrl('mails_mail_mailreceived_detail', array('id' => $mail->getId())));
        }

        //Si la requête est en GET
        return $this->render('MailsAdminBundle:Admin:mailreceived_edit.html.twig', array(
        'form'   => $form->createView(),
        'mail' => $mail 
        ));
    
    }
    
    /**
     * Delete a mail received.
     *
     * @param integer $id mail received id
     * @param Request $request Incoming request
     */
    public function deleteMailreceivedAction($id, Request $request)
    {
        $em = $this->getDoctrine()->getManager();

        // On récupère le mail received d'id $id
        $mail = $em->getRepository('MailsMailBundle:Mail')->findMailReceived($id);

        if (null === $mail) {
        throw new NotFoundHttpException("Le courrier reçu d'id ".$id." n'existe pas.");
        }
        
        //On stocke la référence du courrier reçu dans une varable tampon
        $tempMailreceivedRef = $mail->getReference();
       
        // On supprime notre objet $mail dans la base de données
        $em->remove($mail);
        $em->flush();

        $request->getSession()->getFlashBag()->add('success', 'Le courrier reçu de référence "'.$tempMailreceivedRef.'" a bien été supprimé.');

        //On détruit la variable tampon.
        unset($tempMailreceivedRef);
        
        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_admin_mailreceived'));
    }

}
