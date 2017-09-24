<?php

namespace Mails\MailBundle\MailsentFilter;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Mails\MailBundle\Filter\MailsFilter;

class MailsentFilterListener implements EventSubscriberInterface
{
  /*protected $filter;

  public function __construct(MailsFilter $filter)
  {
    $this->filter = $filter;
  }*/

  // La méthode de l'interface que l'on doit implémenter, à définir en static
  static public function getSubscribedEvents()
  {
    // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
    return array(
      'mails_mail.mailsent.filter_mailsent' => array('processGetMailsentDatas', 2),
      //'autre.evenement'                     => 'autreMethode',
    );
  }

  public function processGetMailsentDatas(MailsentFilterEvent $event)
  {
      // On récupère les données du courrier envoyé depuis l'évènement mails_mail.mailsent.filter_mailsent
        $days = $event->getNbDaysBefore();
        $reception = $event->getReceived();
        $expediteur = $event->getMailsentSender();
        $destinataire = $event->getMailsentRecipient();
        $numItems = \Mails\MailBundle\Filter\MailsFilter::NUM_ITEMS;
        $mail = $event->getMailsent();

    // On active la surveillance si l'auteur du message est dans la liste
    if (in_array($event->getUser()->getId(), $this->listUsers)) {
      
      // On envoie un e-mail à l'administrateur
      $this->processor->notifyEmail($event->getMessage(), $event->getUser());

      // On censure le message
      $message = $this->processor->censorMessage($event->getMessage());

      // On enregistre le message censuré dans l'event
      $event->setMessage($message);
    }
  }

  /*public function autreMethode()
  {
    // ...
  }*/
}