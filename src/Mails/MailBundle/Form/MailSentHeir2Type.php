<?php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Mails\UserBundle\Form\UserType;

class MailSentHeir2Type extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
         ->add('dateEnvoi','datetime')
         ->remove('actor')
         ->remove('user')
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