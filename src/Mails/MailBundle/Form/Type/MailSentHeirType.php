<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailSentHeirType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
         ->remove('dateEnvoi','datetime')
        ;
  }

  public function getName()
  {
    return 'mails_mailsent_heir';
  }

  public function getParent()
  {
    return new MailSentType();
  }
}
