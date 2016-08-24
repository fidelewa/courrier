<?php

namespace Mails\MailBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MailReceivedType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('dateReception','datetime')
            ->add('actor', 'entity', array(
        'class'    => 'MailsMailBundle:Actor',
        'choice_label' => 'name',
        'multiple' => false,
        'expanded' => false
      ))
            ->remove('user', 'entity', array(
        'class'    => 'MailsUserBundle:User',
        'choice_label' => 'username',
        'multiple' => false,
        'expanded' => false
      ))
        ;

    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\MailReceived'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mails_mailbundle_mailreceived';
    }
}
