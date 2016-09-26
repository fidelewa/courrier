<?php
namespace Mails\MailBundle\HandlerMailsData;

class HandlerMailsData 
{
    private $filter;
    private $session;

    public function __construct(\Mails\MailBundle\Filter\MailsFilter $filter, \Symfony\Component\HttpFoundation\Session\Session $session)
    {
        $this->filter = $filter;
        $this->session = $session;
    }
    
    public function processMailsData(\Mails\MailBundle\Entity\Mail $mail, $person, $method)
    {
      // On récupère le nombre de jours, la reception et le traitement du courrier reçu
      $days = $mail->getNbDaysBefore();
      $reception = $mail->getReceived();
      $traitement = $mail->getTreated();

      // On purge la session 
      $this->session->clear();

      // On défini l'attribut de session mail
      $this->session->set('mail', $mail);

      if($method === 'filtreMailreceived')
      {
        // On récupère tous les courriers reçus, filtrés par jour, par reception, par traitement et par user courant
        $allmailreceivedByFilter = $this->filter->filtreMailreceived($days, $reception, $traitement, $person);

        // On défini l'attribut de session allmailreceivedByFilter
        $this->session->set('allmailreceivedByFilter', $allmailreceivedByFilter);
      }
      elseif($method === 'filtreMailreceivedByUser')
      {
        //On récupère tous les courriers reçus, filtrés par date, par reception, par user et par traitement
        $allMailreceivedFilterByUser = $this->filter->filtreMailreceivedByUser($days, $reception, $person, $traitement);

        // On défini les attributs de session allMailreceivedFilterByUser et user
        $this->session->set('allMailreceivedFilterByUser', $allMailreceivedFilterByUser);
        $this->session->set('user', $person);
      }
      elseif($method === 'filtreMailreceivedByActor')
      {
        //On récupère tous les courriers reçus, filtrés par date, par reception, par user et par traitement
        $allMailreceivedFilterByActor = $this->filter->filtreMailreceivedByActor($days, $reception, $person, $traitement);

        // On défini les attributs de session allMailreceivedFilterByUser et user
        $this->session->set('allMailreceivedFilterByActor', $allMailreceivedFilterByActor);
        $this->session->set('contact', $person);
      }
      

    }

}
