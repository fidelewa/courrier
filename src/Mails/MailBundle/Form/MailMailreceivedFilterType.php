<?php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailMailreceivedFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->remove('reference','text')
        ->remove('objet', 'text')
        ->remove('nombrePiecesJointes','text')
        ->remove('mailreceived', new MailReceivedType())
        ->remove('dateEdition','datetime')
        ->add('nbDaysBefore', new IntegerType())
        ->remove('save',      'submit')
        ->add('rechercher',      'submit')
        ;
  }

  public function getName()
  {
    return 'mails_mailbundle_mailreceived_filter';
  }

  public function getParent()
  {
    return new MailMailreceivedType();
  }
}