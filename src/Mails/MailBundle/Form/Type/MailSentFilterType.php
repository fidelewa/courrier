<?php
// src/OC/PlatformBundle/Form/AdvertEditType.php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailSentFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->remove('reference', 'text')
        ->remove('dateEdition','datetime')
        ->remove('nombrePiecesJointes','text')
        ->remove('objet','text')
        ->add('nbDaysBefore', new IntegerType())
        ->add('mailsent', new MailSentHeirType())
        ->remove('save',      'submit')
        ->add('rechercher',      'submit')
        
        ;
  }

  public function getName()
  {
    return 'mails_mailsent_filter';
  }

  public function getParent()
  {
    return new MailMailsentType();
  }
}