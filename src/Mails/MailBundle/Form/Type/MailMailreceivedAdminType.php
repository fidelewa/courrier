<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailMailreceivedAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('dateEdition', 'datetime')
        ->add('mailreceived', new MailReceivedHeir2Type())
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailreceived_admin';
    }

    public function getParent()
    {
        return new MailMailreceivedType();
    }
}
