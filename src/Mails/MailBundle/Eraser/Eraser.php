<?php
namespace Mails\MailBundle\Eraser;

class Eraser 
{
    private $em;

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

    public function deleteContactAndAllHisMails($actor, $allMailsentByActor, $allMailreceivedByActor)
    {
        if((empty($allMailsentByActor)) && (!empty($allMailreceivedByActor)))
        {
            foreach($allMailreceivedByActor as $mailreceivedByActor)
            {
                // On supprime tous les courriers reçus par l'user spécifié 
                $this->em->remove($mailreceivedByActor);
            }
            
            //On supprime le contact spécifié
            $this->em->remove($actor);
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        elseif((!empty($allMailsentByActor)) && (empty($allMailreceivedByActor)))
        {
            foreach($allMailsentByActor as $mailsentByActor)
            {
                // On supprime tous les courriers envoyés par l'user spécifié 
                $this->em->remove($mailsentByActor);
            }
            
            //On supprime le contact spécifié
            $this->em->remove($actor);
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        elseif((empty($allMailsentByActor)) && (empty($allMailreceivedByActor))) 
        {
            //On supprime le contact spécifié
            $this->em->remove($actor);
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
        else
        {
            foreach($allMailreceivedByActor as $mailreceivedByActor)
            {
                // On supprime tous les courriers reçus par le contact spécifié 
                $this->em->remove($mailreceivedByActor);
            }
            
            foreach($allMailsentByActor as $mailsentByActor)
            {
                // On supprime tous les courriers envoyé par le contact spécifié 
                $this->em->remove($mailsentByActor);
            }
        
            //On supprime le contact spécifié
            $this->em->remove($actor);
        
            //On exécute ces opérations de suppression
            $this->em->flush();
        }
    }
}
