<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class MailSentFilterType extends AbstractType
{
    
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('reference', 'text')
        ->remove('dateEdition', 'datetime')
        ->remove('nombrePiecesJointes', 'text')
        ->remove('objet', 'text')
        ->add('nbDaysBefore', IntegerType::class)
        ->add('mailsent', MailsentRemovedateEnvoiAndSecretaryType::class, array('adminCompany' => $options['adminCompany']))
        ->remove('save', 'submit')
        ->add('rechercher', SubmitType::class)
        
        ;
    }

    public function getBlockPrefix()
    {
        return 'mails_mailsent_filter';
    }

    public function getParent()
    {
        return MailMailsentType::class;
    }
}
