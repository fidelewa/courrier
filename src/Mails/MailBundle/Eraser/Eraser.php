<?php
namespace Mails\MailBundle\Eraser;

class Eraser 
{
    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function deleteUserAndAllHisMails($user, $allMailsentByUser, $allMailreceivedByUser)
    {

        if((empty($allMailsentByUser)) && (!empty($allMailreceivedByUser)))
        {
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $this->em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $this->em->remove($user);
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        elseif((!empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $this->em->remove($mailsentByUser);
            }

            //On supprime l'user spécifié
            $this->em->remove($user);

            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        elseif((empty($allMailsentByUser)) && (empty($allMailreceivedByUser)))
        {
            //On supprime l'user spécifié
            $this->em->remove($user);

            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        else
        {
            foreach($allMailsentByUser as $mailsentByUser)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $this->em->remove($mailsentByUser);
            }
            
            foreach($allMailreceivedByUser as $mailreceivedByUser)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $this->em->remove($mailreceivedByUser);
            }
            
            //On supprime l'user spécifié
            $this->em->remove($user);
        
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
    }

}