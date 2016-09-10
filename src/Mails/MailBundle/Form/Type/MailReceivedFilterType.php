<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailReceivedFilterType extends AbstractType
{
  public function buildForm(FormBuilderInterface $builder, array $options)
  {
    $builder
        ->remove('reference', 'text')
        ->remove('dateEdition','datetime')
        ->remove('nombrePiecesJointes','text')
        ->remove('objet','text')
        ->add('nbDaysBefore', new IntegerType())
        ->add('mailreceived', new MailReceivedHeirType())
        ->remove('save',      'submit')
        ->add('rechercher',      'submit')
        ;
  
  }

  public function getName()
  {
    return 'mails_mailreceived_filter';
  }

  public function getParent()
  {
    return new MailMailreceivedType();
  }
}