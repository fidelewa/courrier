<?php

namespace Mails\MailBundle\Manager;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Mails\MailBundle\Paginator\MailsPaginator;
use Symfony\Component\HttpFoundation\Request;
use Mails\UserBundle\Entity\User;
use Mails\MailBundle\Entity\Company;

class ListMailUserManager
{
    private $nbPageCalculator;
    private $paginator;

    public function __construct($nbPageCalculator, $paginator)
    {
        $this->nbPageCalculator = $nbPageCalculator;
        $this->paginator = $paginator;
    }

    /**
     * @param integer $page the page number
     * @param \Mails\UserBundle\Entity\User || null $user the current user
     * @param \Symfony\Component\HttpFoundation\Request $request the current request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse || array()
     */
    public function manageListMailsentByUserRole($page, User $user, Request $request)
    {
        //Si Utilisateur authentifié
        if ($user !== null) {
            //On récupère les roles de l'user
            $userRoles = $user->getRoles();

            //En fonction du profil on fait la pagination de l'index des courriers envoyés
            if (in_array("ROLE_SECRETAIRE", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $this->paginator
                               ->pageIndexMailsentBySecretary($page, MailsPaginator::NUM_ITEMS, $user);

                // On calcule le nombre total de pages en fonction du nombre total de courriers envoyé
                $nombreTotalPages = $this->nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);

                if ($page > $nombreTotalPages) {
                    $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'vous n\'avez pour le moment aucune liste de courrier envoyés à enregistrer !');
                    return new RedirectResponse($request->getBaseUrl().'/');
                }

                return array('mails' => $listMailsSent,'nbPages' => $nombreTotalPages,'page' => $page);
            }
            if (in_array("ROLE_ADMINISTRATEUR", $userRoles) || in_array("ROLE_SUPER_ADMIN", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $this->paginator
                               ->pageIndexMailsentNotValidated($page, MailsPaginator::NUM_ITEMS, $user);

                // On calcule le nombre total de pages en fonction du nombre total de courriers envoyé
                $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);

                if ($page > $nombreTotalPages) {
                    $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'vous n\'avez pour le moment aucune liste de courrier envoyés à valider !');
                    return new RedirectResponse($request->getBaseUrl().'/');
                }

                return array('mails' => $listMailsSent,'nbPages' => $nombreTotalPages,'page' => $page);
            }
        }
    }

    /**
     * @param integer $page the page number
     * @param \Symfony\Component\HttpFoundation\Request $request the current request
     * @param \Mails\MailBundle\Entity\Company $userCompany the company of the current user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse || array()
     */
    public function manageListMailsent($page, Request $request, Company $userCompany)
    {
        // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsSent = $this->paginator->pageIndexMailsent($page, MailsPaginator::NUM_ITEMS, $userCompany);

            /* On calcule le nombre total de pages grâce
            au count($listMailsSent) qui retourne le nombre total de courriers envoyé */
            $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsSent, $page);

        if ($page > $nombreTotalPages) {
            $request
                ->getSession()->getFlashBag()->add('danger', 'Il n\'y a aucune liste de courrier envoyés !');
            return new RedirectResponse($request->getBaseUrl().'/');
        }
            
        return array('mails' => $listMailsSent,'nbPages' => $nombreTotalPages,'page' => $page);
    }

    /**
     * @param integer $page the page number
     * @param \Symfony\Component\HttpFoundation\Request $request the current request
     * @param \Mails\MailBundle\Entity\Company $userCompany the company of the current user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse || array()
     */
    public function manageListMailreceived($page, Request $request, Company $userCompany)
    {
        // On pagine la liste des courriers reçus en fonction des critères spécifiés
            $listMailsReceived = $this->paginator->pageIndexMailreceived($page, MailsPaginator::NUM_ITEMS, $userCompany);

            /* On calcule le nombre total de pages en fonction de la liste des courriers reçus */
            $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);

        if ($page > $nombreTotalPages) {
            $request
                ->getSession()->getFlashBag()->add('danger', 'Il n\'y a aucune liste de courrier reçu !');
            return new RedirectResponse($request->getBaseUrl().'/');
        }

        return array('mails' => $listMailsReceived, 'nombreTotalPages' => $nombreTotalPages, 'page' => $page);
    }

    /**
     * @param integer $page the page number
     * @param \Mails\UserBundle\Entity\User || null $user the current user
     * @param \Symfony\Component\HttpFoundation\Request $request the current request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse || array()
     */
    public function manageListMailreceivedByUserRole($page, $user, \Symfony\Component\HttpFoundation\Request $request)
    {
        //Utilisateur authentifié ou non
        if ($user !== null) {
            //On récupère les roles de l'user
            $userRoles = $user->getRoles();
            
            if (in_array("ROLE_SECRETAIRE", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $this->paginator
                ->pageIndexMailreceivedBySecretary($page, MailsPaginator::NUM_ITEMS, $user);

                // On calcule le nombre total de pages en fonction du nombre total de courriers reçu
                $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);

                if ($page > $nombreTotalPages) {
                    $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'vous n\'avez pour le moment aucune liste de courrier reçus à enregistrer !');
                    return new RedirectResponse($request->getBaseUrl().'/');
                }

                return array('mails' => $listMailsReceived, 'nombreTotalPages' => $nombreTotalPages, 'page' => $page);
            }
            if (in_array("ROLE_ADMINISTRATEUR", $userRoles) || in_array("ROLE_SUPER_ADMIN", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $this->paginator
                                   ->pageIndexMailreceivedNotValidated($page, MailsPaginator::NUM_ITEMS, $user);

                // On calcule le nombre total de pages en fonction du nombre total de courriers reçu
                $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);

                if ($page > $nombreTotalPages) {
                    $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('danger', 'vous n\'avez pour le moment aucune liste de courrier reçus à valider !');
                    return new RedirectResponse($request->getBaseUrl().'/');
                }

                return array('mails' => $listMailsReceived, 'nombreTotalPages' => $nombreTotalPages, 'page' => $page);
            }
        }
    }
}
