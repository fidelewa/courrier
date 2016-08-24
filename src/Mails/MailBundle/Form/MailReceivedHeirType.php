<?php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Mails\UserBundle\Form\UserType;

class MailReceivedHeirType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
         ->remove('dateReception','datetime')
        ;
  }

  public function getName()
  {
    return 'mails_mailreceived_heir';
  }

  public function getParent()
  {
    return new MailReceivedType();
  }
}