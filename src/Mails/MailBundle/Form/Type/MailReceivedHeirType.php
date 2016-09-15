<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

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
