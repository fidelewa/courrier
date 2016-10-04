<?php
namespace Mails\MailBundle\Lister;

class MailsLister 
{
    const NUM_ITEMS = 1;

    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function listAdminActor()
    {
        // On récupère la liste de tous les contacts
                $listActor = $this
                        ->em
                        ->getRepository('MailsMailBundle:Actor')
                        ->findAll()
                ;
                            
        $this->em->flush();

        return $listActor;
    }

    public function listAdminUser()
    {
        // On récupère la liste de tous les utilisateurs
                $listUser = $this
                        ->em
                        ->getRepository('MailsUserBundle:User')
                        ->findAll()
                ;
                            
        $this->em->flush();

        return $listUser;
    }

    public function listAdminMailsent($pageNumber, $itemsNumber, $admin)
    {
        // On récupère la liste de tous les courriers envoyés par l'administrateur spécifié
        $listMailsSent = $this
                        ->em
                        ->getRepository('MailsMailBundle:Mail')
                        ->findAllMailSent($pageNumber, $itemsNumber, $admin)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailsSent;
    }

    public function listAdminMailreceived($pageNumber, $itemsNumber, $admin)
    {
        // On récupère la liste de tous les courriers reçus par l'administrateur spécifié
        $listMailsReceived  = $this
                            ->em
                            ->getRepository('MailsMailBundle:Mail')
                            ->findAllMailReceived($pageNumber, $itemsNumber, $admin)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailsReceived;
    }
}
