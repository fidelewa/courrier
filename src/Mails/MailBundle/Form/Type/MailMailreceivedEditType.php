<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailMailreceivedEditType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('dateEdition', 'datetime')
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailreceived_edit';
    }

    public function getParent()
    {
        return new MailMailreceivedType();
    }
}
