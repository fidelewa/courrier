<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailMailsentSecretaryType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder

        ->remove('reference')
        ->remove('objet')
        ->remove('mailsent')
        ->remove('nombrePiecesJointes')
        ;

  }

  public function getName()
  {
    return 'mails_mailbundle_mailsent_secretary';
  }

  public function getParent()
  {
    return new MailMailsentType();
  }
}