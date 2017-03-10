<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;

class MailsentSecretaryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->remove('reference')
            ->remove('objet')
            ->remove('mailsent')
            ->remove('nombrePiecesJointes')
            ->add('enregistrer', SubmitType::class)
            ->remove('save', 'submit')
        ;
    }

    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailsent_secretary';
    }

    public function getParent()
    {
        return MailMailsentType::class;
    }
}
