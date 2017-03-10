<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MailMailsentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', TextType::class)
            ->add('objet', TextType::class)
            ->add('dateEdition', DateTimeType::class)//apparait pour la date d'enregistrement du courrier
            ->add('nombrePiecesJointes', TextType::class)
            ->add('mailsent', MailSentType::class, array('adminCompany' => $options['adminCompany']))
            ->add('save', SubmitType::class)
            
        ;
        
        // On ajoute une fonction qui va écouter l'évènement PRE_SET_DATA
        $builder->addEventListener(
            FormEvents::PRE_SET_DATA,
            function (FormEvent $event) {
                // On récupère notre objet Mail sous-jacent
                $mail = $event->getData();

                if (null === $mail) {
                    return;
                }

                if (!$mail->getReceived() || null === $mail->getId()) {
                    $event->getForm()->add('received', CheckboxType::class, array('required' => false));
                } else {
                    $event->getForm()->remove('received');
                }
            }
        );
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\Mail',
            'adminCompany' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mail';
    }
}
