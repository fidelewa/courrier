<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailreceivedEditType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('dateEdition', 'datetime')
        ->add('mailreceived', MailreceivedRemoveSecretaryType::class, array('adminCompany' => $options['adminCompany']))
        ;
    }

    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailreceived_edit';
    }

    public function getParent()
    {
        return MailMailreceivedType::class;
    }
}
