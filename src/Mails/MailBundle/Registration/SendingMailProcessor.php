<?php

namespace Mails\MailBundle\Registration;

//use Symfony\Component\Security\Core\User\UserInterface;

class SendingMailProcessor
{
  protected $mailer;
  /** @var string */
  protected $senderName;

    /**
     * @param \Swift_Mailer $mailer
     * @param string $senderName
     */
  public function __construct(\Swift_Mailer $mailer, $senderName)
  {
    $this->mailer = $mailer;
    $this->senderName = $senderName;
  }

  // Méthode pour notifier par e-mail un utilisateur nouvellement inscris
  public function notifyEmail(\Mails\UserBundle\Entity\User $user)
  {

    $message = \Swift_Message::newInstance()
      ->setSubject("Inscription OK")
      ->setFrom('webmaster@mymail.com')
      ->setTo($user->getEmail())
      ->setBody(
      "Bonjour '".$user->getUsername()."',
      Toute l'équipe du site se joint à moi pour vous souhaiter
      la bienvenue sur notre site !
      Revenez nous voir souvent !
      
      Le $this->senderName."
      );

    $this->mailer->send($message);
  }

}