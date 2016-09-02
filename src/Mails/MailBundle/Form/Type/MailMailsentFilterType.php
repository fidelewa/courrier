<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailMailsentFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->remove('reference','text')
        ->remove('objet', 'text')
        ->remove('nombrePiecesJointes','text')
        ->remove('mailsent', new MailSentType())
        ->remove('dateEdition','datetime')
        ->add('nbDaysBefore', new IntegerType())
        ->remove('save',      'submit')
        ->add('rechercher',      'submit')
        ;
  }

  public function getName()
  {
    return 'mails_mailbundle_mailsent_filter';
  }

  public function getParent()
  {
    return new MailMailsentType();
  }
}