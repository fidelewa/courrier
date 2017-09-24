<?php

namespace Mails\MailBundle\MailsentFilter;

use Symfony\Component\EventDispatcher\Event;

class MailsentFilterEvent extends Event
{

  protected $mail;

  public function __construct(\Mails\MailBundle\Entity\Mail $mail)
  {
    $this->mail = $mail;
  }

  // Le listener doit avoir accès à l'identifiant du courrier envoyé
  public function getMailsent()
  {
    return $this->mail;
  }

  // Le listener doit avoir accès au nombre de jours du courrier envoyé
  public function getNbDaysBefore()
  {
    return $this->mail->getNbDaysBefore();
  }

  // Le listener doit avoir accès au status de reception du courrier envoyé
  public function getReceived()
  {
    return $this->mail->getReceived();
  }

  // Le listener doit avoir accès au destinataire du courrier envoyé
  public function getMailsentRecipient()
  {
    return $this->mail->getMailsent()->getActor()->getName();
  }

  // Le listener doit avoir accès à l'expéditeur du courrier envoyé
  public function getMailsentSender()
  {
    return $this->mail->getMailsent()->getUser()->getUsername();
  }

}