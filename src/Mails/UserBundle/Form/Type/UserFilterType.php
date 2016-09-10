<?php

namespace Mails\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use FOS\UserBundle\Form\Type\RegistrationFormType;
use Mails\UserBundle\Entity\User;

class UserFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->remove('email', 'email')
        ->remove('username', null)
        ->remove('plainPassword', 'repeated')
        ->remove('save',      'submit')
        ->add('rechercher',      'submit')
        ;
  }

  public function getName()
  {
    return 'mails_userbundle_user_filter';
  }

  public function getParent()
  {
    return new RegistrationFormType(get_class(new User()));
  }
}