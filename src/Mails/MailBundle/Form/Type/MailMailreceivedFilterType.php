<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailMailreceivedFilterType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('reference', 'text')
        ->remove('objet', 'text')
        ->remove('nombrePiecesJointes', 'text')
        ->remove('mailreceived', MailReceivedType::class, array('adminCompany' => $options['adminCompany']))
        ->remove('dateEdition', 'datetime')
        ->add('nbDaysBefore', IntegerType::class)
        ->remove('save', 'submit')
        ->add('rechercher', SubmitType::class)
        ;
    }

    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailreceived_filter';
    }

    public function getParent()
    {
        return MailMailreceivedType::class;
    }
}
