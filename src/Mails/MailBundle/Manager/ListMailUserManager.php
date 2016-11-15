<?php

namespace Mails\MailBundle\Manager;

use Symfony\Component\HttpFoundation\RedirectResponse;

class ListMailUserManager
{
    private $nbPageCalculator;

    public function __construct($nbPageCalculator)
    {
        $this->nbPageCalculator = $nbPageCalculator;
    }

    public function manageListMailsentByUserRole($user, $page, $request, Mails\MailBundle\Paginator\MailsPaginator $paginator)
    {
        //Utilisateur authentifié ou non
        if ($user !== null) {
            //On récupère les roles de l'user
            $userRoles = $user->getRoles();

            //En fonction du profil on fait la pagination de l'index des courriers envoyés
            if (in_array("ROLE_SECRETAIRE", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsSent = $paginator
                               ->pageIndexMailsentBySecretary($page, $paginator::NUM_ITEMS, $user);

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
                $listMailsSent = $paginator
                               ->pageIndexMailsentNotValidated($page, $paginator::NUM_ITEMS, $user);

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
        } else {
            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsSent = $paginator->pageIndexMailsent($page, $paginator::NUM_ITEMS);

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
    }

    public function manageListMailreceivedByUserRole($user, $page, $request, Mails\MailBundle\Paginator\MailsPaginator $paginator)
    {
        //Utilisateur authentifié ou non
        if ($user !== null) {
            //On récupère les roles de l'user
            $userRoles = $user->getRoles();
            
            if (in_array("ROLE_SECRETAIRE", $userRoles)) {
                // On récupère notre objet Paginator en fonction des critères spécifiés
                $listMailsReceived = $paginator
                ->pageIndexMailreceivedBySecretary($page, $paginator::NUM_ITEMS, $user);

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
                $listMailsReceived = $paginator
                                   ->pageIndexMailreceivedNotValidated($page, $paginator::NUM_ITEMS, $user);

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
        } else {
            // On récupère notre objet Paginator en fonction des critères spécifiés
            $listMailsReceived = $paginator->pageIndexMailreceived($page, $paginator::NUM_ITEMS);

            /* On calcule le nombre total de pages grâce
            au count($listMailsReceived) qui retourne le nombre total de courriers reçus */
            $nombreTotalPages= $this->nbPageCalculator->calculateTotalNumberPage($listMailsReceived, $page);

            if ($page > $nombreTotalPages) {
                $request
                ->getSession()->getFlashBag()->add('danger', 'Il n\'y a aucune liste de courrier reçu !');
                return new RedirectResponse($request->getBaseUrl().'/');
            }

            return array('mails' => $listMailsReceived, 'nombreTotalPages' => $nombreTotalPages, 'page' => $page);
        }
    }
}
