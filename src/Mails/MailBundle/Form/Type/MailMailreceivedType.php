<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailMailreceivedType extends AbstractType
{
    private $adminCompany;

    /**
     * @param string $class The User class name
     */
    public function __construct($adminCompany)
    {
        $this->adminCompany = $adminCompany;
    }


    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('reference', 'text')
            ->add('objet', 'text')
            ->add('dateEdition', 'datetime')
            ->add('nombrePiecesJointes', 'text')
            ->add('received', 'checkbox', array('required' => false))
            ->add('mailreceived', new MailReceivedType($this->adminCompany))
            ->add('save', 'submit')
            //->add('nbDaysBefore','text')
            //->add('mailsent', new MailSentType())
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
                    $event->getForm()->add('received', 'checkbox', array('required' => false));
                } else {
                    $event->getForm()->remove('received');
                }
            
                if (!$mail->getTreated() || null === $mail->getId()) {
                    $event->getForm()->add('treated', 'checkbox', array('required' => false));
                } else {
                    $event->getForm()->remove('treated');
                }
            }
        );
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\Mail'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mails_mailbundle_mail';
    }
}
