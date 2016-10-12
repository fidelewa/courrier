<?php

namespace Mails\MailBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * MailRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class MailRepository extends EntityRepository
{
    public function findLatestMailsSent($limit)
    {
        //Permet de récupérer les $limit derniers courriers envoyés

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
        ;

        $query = $qb->getQuery();
        $query->useQueryCache(true);
        $query->useResultCache(true);
        $query->setResultCacheLifetime(5);
        
        return $query->getResult();
    }
    
    public function findLatestMailsReceived($limit)
    {
        //Permet de récupérer les $limit derniers courriers reçus

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit);
        
        $query = $qb->getQuery();
        $query->useQueryCache(false);
        $query->useResultCache(true);
        $query->setResultCacheLifetime(5);
        
        return $query->getResult();
        ;
    }
    
    public function findMailSent($id)
    {
        //Permet de récupérer un courrier envoyé spécifique

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('m.id = :id')
            ->setParameter('id', $id);
        
        return $qb
                ->getQuery()
                ->getSingleResult()
        ;
    }
    
    public function findMailReceived($id)
    {
        //Permet de récupérer un courrier reçu spécifique
        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('m.id = :id')
            ->setParameter('id', $id);
        
        return $qb
                ->getQuery()
                ->getSingleResult()
        ;
    }
    
    public function getMailsSent($page, $nbPerPage)
    {
        //Permet de récupérer tous les courriers envoyés

        $query = $this->createQueryBuilder('m')
                ->join('m.mailsent', 'ms')
                ->addSelect('ms')
                ->orderBy('m.id', 'DESC')
                ->getQuery()
 
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function getMailsReceived($page, $nbPerPage)
    {
        //Permet de récupérer tous les courriers reçus

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->orderBy('m.id', 'DESC')
                ->getQuery()
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function getMailsSentBySecretary($page, $nbPerPage, $id)
    {
        //Permet de récupérer tous les courriers envoyés non enregistré par la sécrétaire concernée

        $query = $this->createQueryBuilder('m')
                ->join('m.mailsent', 'ms')
                ->addSelect('ms')
                ->where('m.registred = :registred')
                ->orWhere('m.registred IS NULL')
                ->andWhere('m.visaSecretaire = :id')
                ->setParameters(array('registred' => false, 'id' => $id))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
 
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function getMailsReceivedBySecretary($page, $nbPerPage, $id)
    {
        //Permet de récupérer tous les courriers reçus non enregistré par la sécrétaire concernée

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->where('m.registred = :registred')
                ->orWhere('m.registred IS NULL')
                ->andWhere('m.visaSecretaire = :id')
                ->setParameters(array('registred' => false, 'id' => $id))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function getMailsSentNotValidated($page, $nbPerPage, $admin)
    {
        //Permet de récupérer tous les courriers envoyés enregistré et non validé par l'administrateur concerné

        $query = $this
                ->createQueryBuilder('m')
                ->join('m.mailsent', 'ms')
                ->addSelect('ms')
                ->where('m.validated = :validated AND m.registred = :registred')
                ->orWhere('m.validated IS NULL AND m.registred = :registred')
                ->andwhere('ms.user = :admin')
                ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
 
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function getMailsReceivedNotValidated($page, $nbPerPage, $admin)
    {
        //Permet de récupérer tous les courriers reçus enregistré et non validé par l'administrateur concerné

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->where('m.validated = :validated AND m.registred = :registred')
                ->orWhere('m.validated IS NULL AND m.registred = :registred')
                ->andwhere('mr.user = :admin')
                ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
        ;

        $query
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function findAllMailSent($page, $nbPerPage, $admin)
    {
        // Permet de récupérer la liste de tous les courriers envoyés par l'administrateur spécifié

        $query = $this->createQueryBuilder('m')
               ->join('m.mailsent', 'ms')
               ->addSelect('ms')
               ->where('ms.user = :admin')
               ->setParameter('admin', $admin)
               ->orderBy('m.id', 'DESC')
               ->getQuery()
        ;
        
        $query
        ->setFirstResult(($page-1) * $nbPerPage)
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
    
    public function findAllMailReceived($page, $nbPerPage, $admin)
    {
        // Permet de récupérer la liste de tous les courriers reçus par l'administrateur spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.user = :admin')
            ->setParameter('admin', $admin)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
        ;
        
        $qb
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
    
    public function findAllMailreceivedByActorReverse($id)
    {
        // Permet de récupérer la liste de tous les courriers reçus par le contact spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.actor = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }

    public function findAllMailsentByActorReverse($id)
    {
        // Permet de récupérer la liste de tous les courriers envoyé par le contact spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.actor = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }

    public function findAllMailreceivedByActor($id)
    {
        // Permet de récupérer la liste de tous les courriers reçus par le contact concerné

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.actor = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailsentByActor($id)
    {
        // Permet de récupérer la liste de tous les courriers envoyés par le contact concerné

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.actor = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailsentByUser($id)
    {
        // Permet de récupérer la liste de tous les courriers envoyés par l'administrateur spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.user = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailreceivedByUser($id)
    {
        // Permet de récupérer la liste de tous les courriers reçus par l'administrateur spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.user = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailSentByFilter(\Datetime $date, $received, $admin)
    {
        /* Permet de récupérer la liste tous les courriers envoyés,
        filtrés par date et par reception de l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            ->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            ->andwhere('m.received = :received AND ms.user = :admin')
            ->setParameters(array('received' => $received, 'date' => $date, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailReceivedByFilter(\Datetime $date, $received, $treated, $admin)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement de l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            ->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            ->andwhere('m.received = :received AND m.treated = :treated AND mr.user = :admin')
            ->setParameters(array('received' => $received, 'date' => $date, 'treated' => $treated, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }

    
    public function findAllMailSentFilterByUser(\Datetime $date, $received, $id)
    {
        /* Permet de récupérer la liste tous les courriers envoyés,
        filtrés par date et par reception de l'utilisateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            ->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND ms.user = :id')
            // On filtre selon le status de reception et en fonction de l'utilisateur spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailReceivedFilterByUser(\Datetime $date, $received, $user, $treated)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement de l'utilisateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            ->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND mr.user = :user AND m.treated = :treated')
            // On filtre selon le status de reception et de traitement en fonction de l'utilisateur spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'user' => $user, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailSentFilterByActor(\Datetime $date, $received, $id)
    {
        /* Permet de récupérer la liste tous les courriers envoyés,
        filtrés par date et par reception du contact concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            ->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND ms.actor = :id')
            // On filtre selon le status de reception et en fonction du contact spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailReceivedFilterByActor(\Datetime $date, $received, $id, $treated)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement du contact concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            ->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND mr.actor = :id AND m.treated = :treated')
            // On filtre selon le status de reception et de traitement en fonction du contact spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function getAllMailsentFilter(\Datetime $date, $received, $user, $actor, $page, $nbPerPage)
    {
        /* Permet de récupérer tous les courriers envoyés,
        filtrés par date, par reception, par expéditeur et par destinataire spécifié */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->join('ms.user', 'u')
            ->addSelect('u')
            ->join('ms.actor', 'a')
            ->addSelect('a')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            ->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND u.username = :user AND a.name = :actor')
            // On filtre selon le status de reception et en fonction de l'expéditeur et du destinataire spécifiés
            ->setParameters(array('received' => $received, 'date' => $date, 'user' => $user, 'actor' => $actor))
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
        
        $qb
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;
        
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
    
    public function getAllMailreceivedFilter(\Datetime $date, $received, $actor, $user, $treated, $page, $nbPerPage)
    {
        /* Permet de récupérer tous les courriers reçus,
         filtrés par date, par reception, par traitement, par expéditeur et par destinataire spécifié */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->join('mr.user', 'u')
            ->addSelect('u')
            ->join('mr.actor', 'a')
            ->addSelect('a')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            ->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND u.username = :user AND a.name = :actor AND m.treated = :treated') // On filtre selon le status de reception et de traitement en fonction de l'expéditeur et du destinataire spécifiés
            ->setParameters(array('received' => $received, 'date' => $date,
            'user' => $user, 'actor' => $actor, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ;
        
        $qb
        // On définit le courrier "départ" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "départ" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;
        
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
    
    public function findAllMailSentNotValidated($admin, $limit)
    {
        /* Permet de récupérer la liste des courriers envoyés enregistrés
        et non validés par l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('m.validated IS NULL AND m.registred = :registred')
            ->orWhere('m.validated = :validated AND m.registred = :registred')
            ->andwhere('ms.user = :admin')
            ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailReceivedNotValidated($admin, $limit)
    {
        /* Permet de récupérer la liste des courriers reçus enregistrés
        et non validés par l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('m.validated = :validated AND m.registred = :registred')
            ->orWhere('m.validated IS NULL AND m.registred = :registred')
            ->andwhere('mr.user = :admin')
            ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailSentNotRegistred($id, $limit)
    {
        // Permet de récupérer la liste des courriers envoyés non enregistrés par la sécrétaire concerné

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('m.registred = :registred')
            ->orWhere('m.registred IS NULL')
            ->andwhere('m.visaSecretaire = :id')
            ->setParameters(array('registred' => false, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
    
    public function findAllMailReceivedNotRegistred($id, $limit)
    {
        // Permet de récupérer la liste des courriers reçus non enregistrés par la sécrétaire concerné

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('m.registred = :registred')
            ->orWhere('m.registred IS NULL')
            ->andwhere('m.visaSecretaire = :id')
            ->setParameters(array('registred' => false, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
}
