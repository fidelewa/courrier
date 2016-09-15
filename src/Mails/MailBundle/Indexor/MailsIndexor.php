<?php
namespace Mails\MailBundle\Indexor;

class MailsIndexor 
{
    const NUM_ITEMS = 4;

    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function indexLatestMailsent($limit)
    {
        // On récupère les $limit derniers courriers envoyés
        $latestMailsSent = $this
                        ->em
                        ->getRepository('MailsMailBundle:Mail')
                        ->findLatestMailsSent($limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $latestMailsSent;
    }

    public function indexLatestMailreceived($limit)
    {
        // On récupère les $limit derniers courriers reçus
        $latestMailsReceived = $this
                            ->em
                            ->getRepository('MailsMailBundle:Mail')
                            ->findLatestMailsReceived($limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $latestMailsReceived;
    }

    public function indexMailsentNotRegistredBySecretary($idSecretaire, $limit)
    {
        // On récupère les $limit derniers courriers envoyés enregistrés par la sécrétaire
        $listMailsentBySecretary = $this
                                ->em
                                ->getRepository('MailsMailBundle:Mail')
                                ->findAllMailSentNotRegistred($idSecretaire, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailsentBySecretary
        ;
    }

    public function indexMailreceivedNotRegistredBySecretary($idSecretaire, $limit)
    {
        // On récupère les $limit derniers courriers envoyés enregistrés par la sécrétaire
        $listMailreceivedBySecretary = $this
                                    ->em
                                    ->getRepository('MailsMailBundle:Mail')
                                    ->findAllMailReceivedNotRegistred($idSecretaire, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailreceivedBySecretary
        ;
    }

    public function indexMailsentNotValidatedByAdmin($admin, $limit)
    {
        //On récupère tous ses courriers envoyés enregistrés et non validés par l'administrateur concerné
        $listMailsentByAdmin = $this
                            ->em
                            ->getRepository('MailsMailBundle:Mail')
                            ->findAllMailSentNotValidated($admin, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailsentByAdmin
        ;
    }

    public function indexMailreceivedNotValidatedByAdmin($admin, $limit)
    {
        //On récupère tous ses courriers envoyés enregistrés et non validés par l'administrateur concerné
        $listMailreceivedByAdmin = $this
                            ->em
                            ->getRepository('MailsMailBundle:Mail')
                            ->findAllMailReceivedNotValidated($admin, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailreceivedByAdmin
        ;
    }
}
