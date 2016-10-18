<?php

namespace Mails\MailBundle\Calculator;

use Mails\MailBundle\Paginator\MailsPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * The NbPageCalculator.
 * Use for calculate the total number of pages
 *
 * @author Fidele Avi
 */
class NbPageCalculator
{
    public function calculateTotalNumberPage(Paginator $listMails)
    {
        // On calcule le nombre total de pages grâce au count($listMails) qui retourne le nombre total de courriers
        $nombreTotalMails = $listMails->count();
        $nombreMailPage = MailsPaginator::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMails/$nombreMailPage);

        return $nombreTotalPages;
    }

    public function calculateTotalNumberPageByUser(Paginator $listMails, $numItems)
    {
        // On calcule le nombre total de pages grâce au count($listMails) qui retourne le nombre total de courriers
        $nombreTotalMails = $listMails->count();
        $nombreMailPage = $numItems;
        $nombreTotalPages = ceil($nombreTotalMails/$nombreMailPage);
                
        return $nombreTotalPages;
    }


    public function calculateTotalNumberPageByFilter(Paginator $allMailFilter, $nbPerPage)
    {
        /* On calcule le nombre total de pages grâce au
        count($listMailsReceived) qui retourne le nombre total de courriers reçus */
        $nombreTotalMails = $allMailFilter->count();
        $nombreMailreceived = $nbPerPage;
        $nombreTotalPagesByFilter = ceil($nombreTotalMails/$nombreMailreceived);

        return $nombreTotalPagesByFilter;
    }
}
