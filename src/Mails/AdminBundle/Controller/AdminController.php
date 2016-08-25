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
     * Admin actors home page controller.
     */
    public function adminActorAction() 
    {
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On affiche la liste de tous les interlocuteurs
        $listActor = $lister->listAdminActor();

        return $this->render('MailsAdminBundle:Admin:admin_actor.html.twig', array(
            'actors' => $listActor
            ));

    }
    
    /**
     * Admin users home page controller.
     */
    public function adminUserAction() 
    {
        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On affiche la liste de tous les utilisateurs
        $listUser = $lister->listAdminUser();

        return $this->render('MailsAdminBundle:Admin:admin_user.html.twig', array(
            'users' => $listUser
            ));
    }
    
    /**
     * Admin mail sent home page controller.
     */
    public function adminMailsentAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //On récupère l'administrateur courant
        $admin = $this->getUser();

        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère la liste de tous les courriers envoyés par l'administrateur courant
        $listMailsSent = $lister->listAdminMailsent($page, $lister::NUM_ITEMS, $admin);
                
        // On calcule le nombre total de pages grâce au count($listMailsSent) qui retourne le nombre total de courriers envoyé
        $nombreTotalMailsSent = $listMailsSent->count();
        $nombreMailsentPage = $lister::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMailsSent/$nombreMailsentPage); 
                
        if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            //return $this->redirect($this->generateUrl('mails_core_home'));
        }

        return $this->render('MailsAdminBundle:Admin:admin_mailsent.html.twig', array(
            'mailsSentByActor' => $listMailsSent,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));
    }
    
    /**
     * Admin mail received home page controller.
     */
    public function adminMailreceivedAction($page) 
    {
        if ($page < 1) {
        throw new NotFoundHttpException('Page "'.$page.'" inexistante.');
        }
        
        //On récupère l'administrateur courant
        $admin = $this->getUser();

        // On récupère notre service lister
        $lister = $this->get('mails_admin.mail_lister');

        // On récupère la liste de tous les courriers reçus par l'administrateur courant
        $listMailsReceived = $lister->listAdminMailreceived($page, $lister::NUM_ITEMS, $admin);
                
        // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
        $nombreTotalMailsReceived = $listMailsReceived->count();
        $nombreMailreceivedPage = $lister::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMailsReceived/$nombreMailreceivedPage); 
                
        if($page > $nombreTotalPages){
            throw $this->createNotFoundException("La page ".$page." n'existe pas.");
            //return $this->redirect($this->generateUrl('mails_core_home'));
        } 

        return $this->render('MailsAdminBundle:Admin:admin_mailreceived.html.twig', array(
            'mailsReceivedByActor' => $listMailsReceived,
            'nbPages' => $nombreTotalPages,
            'page' => $page,
            ));
    }
    //--------------------------------------------------------------------------    
    /**
     * Edit mail sent controller.
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
     * Delete mail sent controller.
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
     * Edit mail received controller.
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
     * Delete mail received controller.
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
    //------------------------------------------------------------------------------------   
    /**
     * Edit actor controller.
     *
     * @param integer $id Actor id
     * @param Request $request Incoming request
     */
     public function editActorAction($id, Request $request)
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
           return $this->redirect($this->generateUrl('mails_admin_actor'));
        }

        // Si la requête est en GET
        return $this->render('MailsMailBundle:Mail:actor_add.html.twig', array(
        'actorForm'   => $form->createView(),
        'title' => 'Modifier un interlocuteur existant',
        ));
    
     }
     
     /**
     * Delete actor controller.
     *
     * @param integer $id Actor id
     * @param Request $request Incoming request
     */
    public function deleteActorAction($id, Request $request)
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
        return $this->redirect($this->generateUrl('mails_admin_actor'));
    }
    //----------------------------------------------------------------------------------------    
    /**
     * Delete user controller.
     *
     * @param integer $id User id
     * @param Request $request Incoming request
     */
    public function deleteUserAction($id, Request $request)
    {
        // On récupère l'Entity Manager
        $em = $this->getDoctrine()->getManager();
        
        // On récupère l'user par son id
        $user = $em->getRepository('MailsUserBundle:User')->find($id);

        // On récupère tous les courriers envoyés par l'user
        $allMailsentByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailsentByUser($id);
        
        // On récupère tous les courriers reçus par l'user
        $allMailreceivedByUser = $em->getRepository('MailsMailBundle:Mail')->findAllMailreceivedByUser($id);

        if (null === $user) {
        throw new NotFoundHttpException("L'utilisateur d'id ".$id." n'existe pas.");
        }
        
        // On stocke le nom de l'utilisateur dans une variable tampon
        $tempUserName = $user->getUsername();
        
        if((empty($allMailsentByUser)) && (!empty($allMailreceivedByUser)))
        {
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $em->remove($user);
            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((!empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $em->remove($mailsentByUser);
            }

            //On supprime l'user spécifié
            $em->remove($user);

            //On exécute ces opérations de suppression
            $em->flush();
        }
        elseif((empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            //On supprime l'user spécifié
            $em->remove($user);

            //On exécute ces opérations de suppression
            $em->flush();
        }
        else
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $em->remove($mailsentByUser);
            }
            
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $em->remove($user);
        
            //On exécute ces opérations de suppression
            $em->flush();
        }
        
        $request->getSession()->getFlashBag()->add('success', 'L\'utilisateur "'.$tempUserName.'" ainsi que tous ses courriers ont bien été supprimés.');

        // On supprime la variable tampon
        unset($tempUserName);

        // Puis on redirige vers l'accueil
        return $this->redirect($this->generateUrl('mails_admin_user'));
    }
     //------------------------------------------------------------
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

     public function filterMailsentUserAction($id, Request $request)
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

     public function filterMailsentActorAction($id, Request $request)
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

     //-------------------------------------------------------------------------------------
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
     
     public function filterMailreceivedUserAction($id, Request $request)
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
     
     public function filterMailreceivedActorAction($id, Request $request)
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
