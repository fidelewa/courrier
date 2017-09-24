<?php
namespace Mails\MailBundle\Indexor;

class MailsIndexor
{
    const NUM_ITEMS = 1;

    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function indexLatestMailsent($limit, $idCompany)
    {
        //On récupère les $limit derniers courriers envoyés par l'entreprise spécifiée
        $latestMailsSent = $this
                        ->em
                        ->getRepository('MailsMailBundle:Mail')
                        ->findLatestMailsSent($limit, $idCompany)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $latestMailsSent;
    }

    public function indexLatestMailreceived($limit, $idCompany)
    {
        // On récupère les $limit derniers courriers reçus par l'entreprise spécifiée
        $latestMailsReceived = $this
                            ->em
                            ->getRepository('MailsMailBundle:Mail')
                            ->findLatestMailsReceived($limit, $idCompany)
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
                                ->findLatestMailSentNotRegistred($idSecretaire, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailsentBySecretary
        ;
    }

    public function indexMailreceivedNotRegistredBySecretary($idSecretaire, $limit)
    {
        // On récupère les $limit derniers courriers reçus non encore enregistrés par la sécrétaire courante
        $listMailreceivedBySecretary = $this
                                    ->em
                                    ->getRepository('MailsMailBundle:Mail')
                                    ->findLatestMailReceivedNotRegistred($idSecretaire, $limit)
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
                            ->findLatestMailSentNotValidated($admin, $limit)
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
                            ->findLatestMailReceivedNotValidated($admin, $limit)
                ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();

        return $listMailreceivedByAdmin
        ;
    }
}
