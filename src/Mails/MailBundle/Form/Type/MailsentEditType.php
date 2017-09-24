<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailsentEditType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('dateEdition', 'datetime')
        ->add('mailsent', MailsentRemoveSecretaryType::class, array('adminCompany' => $options['adminCompany']))
        ;
    }

    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailsent_edit';
    }

    public function getParent()
    {
        return MailMailsentType::class;
    }
}
