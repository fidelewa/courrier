<?php

namespace Mails\MailBundle\Creator;

use Doctrine\ORM\EntityManager;
use Mails\MailBundle\Entity\Mail;
use Symfony\Component\Form\Form;
use Mails\MailBundle\Entity\MailReceived;
use Mails\MailBundle\Entity\MailSent;
use Mails\UserBundle\Entity\User;

class MailsCreator
{
    private $em;
    private $courier;

    public function __construct(EntityManager $entityManager, Mail $mail)
    {
        $this->em = $entityManager;
        $this->courier = $mail;
    }
    
    public function processCreateMailReceived(Form $form, MailReceived $mailreceived, User $user)
    {
        //On récupère les données saisies via le formulaire concernant notre courrier
        $this->courier = $form->getData();
        
        //On récupère la sécrétaire qui a enregistré le courrier reçu
        $secretary = $this->courier->getMailreceived()->getUser();
            
       //On récupère l'expéditeur du courrier reçu
        $sender = $this->courier->getMailreceived()->getActor();
            
       //On défini l'expéditeur du courrier reçu
        $mailreceived->setActor($sender);
            
       //On défini le nom de la sécrétaire dans le courrier reçu
        $this->courier->setSecretaire($secretary);
            
       //On défini le destinataire du courrier reçu (qui est en faite l'utilisateur qui crée le courrier reçu)
        $recipient = $user;
        $mailreceived->setUser($recipient);
            
       //On défini le courrier reçu
        $this->courier->setMailreceived($mailreceived);
            
       //On enregiste le courrier reçu en BDD
        $this->em->persist($this->courier);
        $this->em->flush();

       // On renvoi le conrrier reçu crée
        return $this->courier;
    }

    public function processCreateMailSent(Form $form, MailSent $mailsent, User $user)
    {
        //On défini notre courrier et on récupère la sécrétaire qui a enregistré le courrier envoyé
        $this->courier = $form->getData();
        $secretary = $this->courier->getMailsent()->getUser();
                            
        //On récupère le destinataire du courrier envoyé
        $recipient = $this->courier->getMailsent()->getActor();
                            
        //On défini le destinataire du courrier envoyé
        $mailsent->setActor($recipient);
                        
        //On défini le nom de la sécrétaire dans le courrier envoyé
        $this->courier->setSecretaire($secretary);
                            
        //On défini l'expéditeur du courrier envoyé (qui est en faite l'utilisateur qui crée le courrier envoyé)
        $sender = $user;
        $mailsent->setUser($sender);
                            
        //On défini le courrier envoyé
        $this->courier->setMailsent($mailsent);
                            
        //On enregiste le courrier envoyé en BDD
        $this->em->persist($this->courier);
        $this->em->flush();

        // On renvoi le conrrier envoyé crée
        return $this->courier;
    }
}
