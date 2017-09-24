<?php

namespace Mails\MailBundle\Registration;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class RegistrationListener implements EventSubscriberInterface
{
  protected $processor;

  public function __construct(SendingMailProcessor $processor)
  {
    $this->processor = $processor;
  }

  // La méthode de l'interface que l'on doit implémenter, à définir en static
  static public function getSubscribedEvents()
  {
    // On retourne un tableau « nom de l'évènement » => « méthode à exécuter »
    return array(
      'fos_user.registration.completed' => array('processMessageRegistration', 2),
      //'autre.evenement'                     => 'autreMethode',
    );
  }

  public function processMessageRegistration(\FOS\UserBundle\Event\FilterUserResponseEvent $event)
  {
    // On vérifie que l'utilisateur est un super-administrateur
    //if ($event->getUser()->isSuperAdmin()) {
    if ($event->getUser()->hasRole('ROLE_ADMINISTRATEUR')) {
      
      // On envoie un e-mail à l'utilisateur nouvellement inscris
      $this->processor->notifyEmail($event->getUser());
    }
  }

  /*public function autreMethode()
  {
    // ...
  }*/
}