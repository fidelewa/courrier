<?php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailMailreceivedSecretaryType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder

        ->remove('reference')
        ->remove('objet')
        ->remove('mailreceived')
        ->remove('nombrePiecesJointes')
        ;

  }

  public function getName()
  {
    return 'mails_mailbundle_mailreceived_secretary';
  }

  public function getParent()
  {
    return new MailMailreceivedType();
  }
}