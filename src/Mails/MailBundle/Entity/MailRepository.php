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
    public function findLatestMailsSent($limit, $idCompany)
    {
        //Permet de récupérer les $limit derniers courriers envoyés par l'entreprise de l'user spécifié

        $qb = $this
            ->createQueryBuilder('m', 'm.id')// ->select('m')->from('MailsMailBundle:Mail', 'm', 'm.id');
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->join('ms.user', 'u')
            ->addSelect('u')
            ->where('u.company = :idCompany')
            ->setParameter('idCompany', $idCompany, \PDO::PARAM_INT)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
        ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_latest_mailsent')
        ;

        return $query->getResult();
    }

    public function findLatestMailsReceived($limit, $idCompany)
    {
        //Permet de récupérer les $limit derniers courriers reçus par l'entreprise de l'user spécifié

        $qb = $this
            ->createQueryBuilder('m', 'm.id')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->join('mr.user', 'u')
            ->addSelect('u')
            ->where('u.company = :idCompany')
            ->setParameter('idCompany', $idCompany, \PDO::PARAM_INT)
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
        ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_latest_mailreceived')
        ;

        return $query->getResult();
    }
// SHOW INFORMATIONS OF A MAIL
    public function findMailSent($id, $company)
    {
        // Permet de récupérer un courrier envoyé spécifique à l'entreprise de l'user spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->join('ms.user', 'u')
            ->addSelect('u')
            ->where('m.id = :id')
            ->andwhere('u.company = :company')
            ->setParameters(array('id' => $id, 'company' => $company))
        ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_mailsent')
        ;

        return $query->getSingleResult();
    }

    public function findMailReceived($id, $company)
    {
        //Permet de récupérer un courrier reçu spécifique de l'entreprise de l'user spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->join('mr.user', 'u')
            ->addSelect('u')
            ->where('m.id = :id')
            ->andwhere('u.company = :company')
            ->setParameters(array('id' => $id, 'company' => $company))
        ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_mailreceived')
        ;

        return $query->getSingleResult();
    }
// SHOW ALL MAILS
    public function getMailsSent($page, $nbPerPage, $idCompany)
    {
        //Permet de récupérer la liste de tous les courriers envoyés par l'entreprise de l'user spécifié

        $query = $this->createQueryBuilder('m')
                ->join('m.mailsent', 'ms')
                ->addSelect('ms')
                ->join('ms.user', 'u')
                ->addSelect('u')
                ->where('u.company = :idCompany')
                ->setParameter('idCompany', $idCompany)
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_mailsent')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }

    public function getMailsReceived($page, $nbPerPage, $idCompany)
    {
        //Permet de récupérer la liste de tous les courriers reçus par l'entreprise de l'user spécifié

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->join('mr.user', 'u')
                ->addSelect('u')
                ->where('u.company = :idCompany')
                ->setParameter('idCompany', $idCompany)
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_mailreceived')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
// SHOW ALL MAILS BY USER
    public function getAllMailSentNotRegistred($page, $nbPerPage, $idSecretaire)
    {
        //Permet de récupérer la liste de tous les courriers envoyés non enregistré par la sécrétaire concernée

        $query = $this->createQueryBuilder('m')
                ->join('m.mailsent', 'ms')
                ->addSelect('ms')
                ->where('m.registred = :registred')
                ->orWhere('m.registred IS NULL')
                ->andWhere('m.secretaire = :idSecretaire')
                ->setParameters(array('registred' => false, 'idSecretaire' => $idSecretaire))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_all_mailsent_not_registred')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }

    public function getAllMailReceivedNotRegistred($page, $nbPerPage, $idSecretaire)
    {
        //Permet de récupérer la liste de tous les courriers reçus non enregistré par la sécrétaire concernée

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->where('m.registred = :registred')
                ->orWhere('m.registred IS NULL')
                ->andWhere('m.secretaire = :idSecretaire')
                ->setParameters(array('registred' => false, 'idSecretaire' => $idSecretaire))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_all_mailreceived_not_registred')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }

    public function getAllMailSentNotValidated($page, $nbPerPage, $admin)
    {
        /* Permet de récupérer la liste de tous les courriers
        envoyés enregistré et non validé par l'administrateur concerné */

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
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_all_mailsent_not_validated')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }

    public function getAllMailReceivedNotValidated($page, $nbPerPage, $admin)
    {
        /* Permet de récupérer la liste de tous les courriers
        reçus enregistré et non validé par l'administrateur concerné */

        $query = $this->createQueryBuilder('m')
                ->join('m.mailreceived', 'mr')
                ->addSelect('mr')
                ->where('m.validated = :validated AND m.registred = :registred')
                ->orWhere('m.validated IS NULL AND m.registred = :registred')
                ->andwhere('mr.user = :admin')
                ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
                ->orderBy('m.id', 'DESC')
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'get_all_mailreceived_not_validated')
        ;

        $query
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($query, true);
    }
// SHOW LIST OF MAILS MANAGED BY ADMINISTRATOR
    public function findAllMailSent($page, $nbPerPage, $admin)
    {
        /* Permet de récupérer la liste de tous les courriers envoyés par l'administrateur spécifié 
        dans l'espace d'administration */

        $query = $this->createQueryBuilder('m')
               ->join('m.mailsent', 'ms')
               ->addSelect('ms')
               ->where('ms.user = :admin')
               ->setParameter('admin', $admin)
               ->orderBy('m.id', 'DESC')
               ->getQuery()
               //->useQueryCache(true)
               //->useResultCache(true, 3600, 'find_all_mailsent')
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
        /* Permet de récupérer la liste de tous les courriers reçus par l'administrateur spécifié
         dans l'espace d'administration*/

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.user = :admin')
            ->setParameter('admin', $admin)
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            //->useQueryCache(true)
            //->useResultCache(true, 3600, 'find_all_mailreceived')
        ;

        $qb
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;

        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
// SHOW LIST OF MAILS OF USER
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

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailreceived_actor_reverse')
        ;

        return $query->getResult();
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

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailsent_actor_reverse')
        ;

        return $query->getResult();
    }

    public function findAllMailsentByUser($id)
    {
        // Permet de récupérer la liste de tous les courriers envoyés par l'user spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('ms.user = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
            ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailsent_user')
        ;

        return $query->getResult();
    }

    public function findAllMailreceivedByUser($id)
    {
        // Permet de récupérer la liste de tous les courriers reçus par l'user spécifié

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('mr.user = :id')
            ->setParameter('id', $id)
            ->orderBy('m.id', 'DESC')
        ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailreceived_user')
        ;

        return $query->getResult();
    }
// SHOW INDEX OF LATEST MAILS BY USER
    public function findLatestMailSentNotValidated($admin, $limit)
    {
        /* Permet de récupérer la liste des courriers envoyés enregistrés
        et non validés par l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m', 'm.id')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('m.validated IS NULL AND m.registred = :registred')
            ->orWhere('m.validated = :validated AND m.registred = :registred')
            ->andwhere('ms.user = :admin')
            ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
        ;

        return $query->getResult();
    }
    
    public function findLatestMailReceivedNotValidated($admin, $limit)
    {
        /* Permet de récupérer la liste des courriers reçus enregistrés
        et non validés par l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m', 'm.id')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('m.validated = :validated AND m.registred = :registred')
            ->orWhere('m.validated IS NULL AND m.registred = :registred')
            ->andwhere('mr.user = :admin')
            ->setParameters(array('validated' => false, 'registred' => true, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
        ;

        return $query->getResult();
    }
    
    public function findLatestMailSentNotRegistred($idSecretaire, $limit)
    {
        // Permet de récupérer la liste des derniers courriers envoyés non enregistrés par la sécrétaire concerné

        $qb = $this
            ->createQueryBuilder('m', 'm.id')
            ->join('m.mailsent', 'ms')
            ->addSelect('ms')
            ->where('m.registred = :registred')
            ->orWhere('m.registred IS NULL')
            ->andwhere('m.secretaire = :idSecretaire')
            ->setParameters(array('registred' => false, 'idSecretaire' => $idSecretaire))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
        ;

        return $query->getResult();
    }
    
    public function findLatestMailReceivedNotRegistred($idSecretaire, $limit)
    {
        // On récupère les $limit derniers courriers reçus non encore enregistrés par la sécrétaire concernée

        $qb = $this
            ->createQueryBuilder('m', 'm.id')
            ->join('m.mailreceived', 'mr')
            ->addSelect('mr')
            ->where('m.registred = :registred')
            ->orWhere('m.registred IS NULL')
            ->andwhere('m.secretaire = :idSecretaire')
            ->setParameters(array('registred' => false, 'idSecretaire' => $idSecretaire))
            ->orderBy('m.id', 'DESC')
            ->setMaxResults($limit)
            ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
        ;

        return $query->getResult();
    }

    public function getAllMailRegistred($idSecretaire)
    {
        // On récupère la liste de tous les courrier enregistrés par la sécrétaire concernée pour les supprimer

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.secretaire', 'u')
            ->addSelect('u')
            ->where('m.secretaire = :idSecretaire')
            ->setParameters(array('idSecretaire' => $idSecretaire))
            ->orderBy('m.id', 'DESC')
            ;
        
        return $qb
                ->getQuery()
                ->getResult()
        ;
    }
//FILTER
    public function filterAllMailsent(\Datetime $date, $received, $admin)
    {
        /* Permet de récupérer la liste de tous les courriers envoyés,
        filtrés par date et par reception de l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m')//Nous avons un QueryBuilder de mail
            ->join('m.mailsent', 'ms', 'ON', null, 'ms.dateEnvoi')
            ->addSelect('ms')
            ->join('ms.actor', 'a', 'ON', null, 'a.name')
            ->addSelect('a')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            //->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            ->andwhere('m.received = :received AND ms.user = :admin')
            ->setParameters(array('received' => $received, 'date' => $date, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
        ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailsent_filter')
        ;

        return $query->getResult();
    }
    
    public function filterAllMailreceived(\Datetime $date, $received, $treated, $admin)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement de l'administrateur concerné */

        $qb = $this
            ->createQueryBuilder('m', 'm.treated')
            ->join('m.mailreceived', 'mr', 'ON', null, 'mr.dateReception')
            ->addSelect('mr')
            ->join('mr.actor', 'a', 'ON', null, 'a.name')
            ->addSelect('a')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            //->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            ->andwhere('m.received = :received AND m.treated = :treated AND mr.user = :admin')
            ->setParameters(array('received' => $received, 'date' => $date, 'treated' => $treated, 'admin' => $admin))
            ->orderBy('m.id', 'DESC')
            ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailreceived_filter')
        ;

        return $query->getResult();
    }

    public function filterAllMailsentByUser(\Datetime $date, $received, $id)
    {
        /* Permet de récupérer la liste tous les courriers envoyés,
        filtrés par date et par reception de l'utilisateur concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms', 'ON', null, 'ms.dateEnvoi')
            ->addSelect('ms')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            //->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND ms.user = :id')
            // On filtre selon le status de reception et en fonction de l'utilisateur spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailsent_filter_user')
        ;

        return $query->getResult();
    }
    
    public function filterAllMailreceivedByUser(\Datetime $date, $received, $user, $treated)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement de l'utilisateur concerné */

        $qb = $this
            ->createQueryBuilder('m', 'm.treated')
            ->join('m.mailreceived', 'mr', 'ON', null, 'mr.dateReception')
            ->addSelect('mr')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            //->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND mr.user = :user AND m.treated = :treated')
            // On filtre selon le status de reception et de traitement en fonction de l'utilisateur spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'user' => $user, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailreceived_filter_user')
        ;

        return $query->getResult();
    }
    
    public function filterAllMailsentByActor(\Datetime $date, $received, $id)
    {
        /* Permet de récupérer la liste tous les courriers envoyés,
        filtrés par date et par reception du contact concerné */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms', 'ON', null, 'ms.dateEnvoi')
            ->addSelect('ms')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            //->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND ms.actor = :id')
            // On filtre selon le status de reception et en fonction du contact spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id))
            ->orderBy('m.id', 'DESC')
            ;
        
        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailsent_filter_actor')
        ;

        return $query->getResult();
    }
    
    public function filterAllMailreceivedByActor(\Datetime $date, $received, $id, $treated)
    {
        /* Permet de récupérer la liste tous les courriers reçu,
        filtrés par date, par reception et par traitement du contact concerné */

        $qb = $this
            ->createQueryBuilder('m', 'm.treated')
            ->join('m.mailreceived', 'mr', 'ON', null, 'mr.dateReception')
            ->addSelect('mr')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            //->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND mr.actor = :id AND m.treated = :treated')
            // On filtre selon le status de reception et de traitement en fonction du contact spécifié
            ->setParameters(array('received' => $received, 'date' => $date, 'id' => $id, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ;

        $query = $qb
                ->getQuery()
                //->useQueryCache(true)
                //->useResultCache(true, 3600, 'find_all_mailreceived_filter_actor')
        ;

        return $query->getResult();
    }
    
    public function getAllMailsentFilter(\Datetime $date, $received, $user, $actor, $page, $nbPerPage)
    {
        /* Permet de récupérer tous les courriers envoyés,
        filtrés par date, par reception, par expéditeur et par destinataire spécifié */

        $qb = $this
            ->createQueryBuilder('m')
            ->join('m.mailsent', 'ms', 'ON', null, 'ms.dateEnvoi')
            ->addSelect('ms')
            ->join('ms.user', 'u', 'ON', null, 'u.username')
            ->addSelect('u')
            ->join('ms.actor', 'a', 'ON', null, 'a.name')
            ->addSelect('a')
            ->where('ms.dateEnvoi <= :date') // Date d'envoi antérieure à :date
            //->orWhere('ms.dateEnvoi IS NULL AND m.dateEdition <= :date')
            // Si la date d'envoi est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND u.username = :user AND a.name = :actor')
            // On filtre selon le status de reception et en fonction de l'expéditeur et du destinataire spécifiés
            ->setParameters(array('received' => $received, 'date' => $date, 'user' => $user, 'actor' => $actor))
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            //->useQueryCache(true)
            //->useResultCache(true, 3600, 'get_all_mailsent_filter')
        ;
        
        $qb
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;
        
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
    
    public function getAllMailreceivedFilterByUser(\Datetime $date, $received, $actor, $user, $treated, $page, $nbPerPage)
    {
        /* Permet de récupérer tous les courriers reçus,
         filtrés par date, par reception, par traitement, par expéditeur et par destinataire spécifié */

        $qb = $this
            ->createQueryBuilder('m', 'm.treated')
            ->join('m.mailreceived', 'mr', 'ON', null, 'mr.dateReception')
            ->addSelect('mr')
            ->join('mr.user', 'u', 'ON', null, 'u.username')
            ->addSelect('u')
            ->join('mr.actor', 'a', 'ON', null, 'a.name')
            ->addSelect('a')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            //->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND u.username = :user AND a.name = :actor AND m.treated = :treated')
            /* On filtre selon le status de reception et de
            traitement en fonction de l'expéditeur et du destinataire spécifiés */
            ->setParameters(array('received' => $received, 'date' => $date, 'user' => $user, 'actor' => $actor, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            //->useQueryCache(true)
            //->useResultCache(true, 3600, 'get_all_mailreceived_filter')
        ;
        
        $qb
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;
        
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }

    public function getAllMailreceivedFilter(\Datetime $date, $received, $actor, $treated, $page, $nbPerPage)
    {
        /* Permet de récupérer tous les courriers reçus,
         filtrés par date, par reception, par traitement, par expéditeur et par destinataire spécifié */

        $qb = $this
            ->createQueryBuilder('m', 'm.treated')
            ->join('m.mailreceived', 'mr', 'ON', null, 'mr.dateReception')
            ->addSelect('mr')
            ->join('mr.actor', 'a', 'ON', null, 'a.name')
            ->addSelect('a')
            ->where('mr.dateReception <= :date') // Date de reception antérieure à :date
            //->orWhere('mr.dateReception IS NULL AND m.dateEdition <= :date')
            // Si la date de reception est sans valeur, on vérifie la date d'édition
            ->andwhere('m.received = :received AND a.name = :actor AND m.treated = :treated')
            /* On filtre selon le status de reception et de
            traitement en fonction de l'expéditeur et du destinataire spécifiés */
            ->setParameters(array('received' => $received, 'date' => $date, 'actor' => $actor, 'treated' => $treated))
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            //->useQueryCache(true)
            //->useResultCache(true, 3600, 'get_all_mailreceived_filter')
        ;
        
        $qb
        // On définit le courrier "envoyé" à partir duquel commencer la liste
        ->setFirstResult(($page-1) * $nbPerPage)
        // Ainsi que le nombre de courrier "envoyé" à afficher sur une page
        ->setMaxResults($nbPerPage)
        ;
        
        // Enfin, on retourne l'objet Paginator correspondant à la requête construite
        // (n'oubliez pas le use correspondant en début de fichier)
        return new Paginator($qb, true);
    }
}
