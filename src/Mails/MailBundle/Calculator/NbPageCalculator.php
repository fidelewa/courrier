<?php
namespace Mails\MailBundle\Calculator;

use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Mails\MailBundle\Paginator\MailsPaginator;
use Doctrine\ORM\Tools\Pagination\Paginator;

class NbPageCalculator 
{
    
    public function calculateTotalNumberPage(Paginator $listMails, $page)
    {
        // On calcule le nombre total de pages grâce au count($listMails) qui retourne le nombre total de courriers
        $nombreTotalMails = $listMails->count();
        $nombreMailPage = MailsPaginator::NUM_ITEMS;
        $nombreTotalPages = ceil($nombreTotalMails/$nombreMailPage); 
                
        if($page > $nombreTotalPages){
            throw new NotFoundHttpException("La page ".$page." n'existe pas.");
        }

        return $nombreTotalPages;
    }

    public function calculateTotalNumberPageByFilter(Paginator $allMailFilter, $page, $nbPerPage)
    {
        // On calcule le nombre total de pages grâce au count($listMailsReceived) qui retourne le nombre total de courriers reçus
        $nombreTotalMails = $allMailFilter->count();
        $nombreMailreceived = $nbPerPage;
        $nombreTotalPagesByFilter = ceil($nombreTotalMails/$nombreMailreceived); 
        
            if($page > $nombreTotalPagesByFilter){
            throw new NotFoundHttpException("Aucune données ne correspond a cette recherche !");
            }
        return $nombreTotalPagesByFilter;
    }
}
