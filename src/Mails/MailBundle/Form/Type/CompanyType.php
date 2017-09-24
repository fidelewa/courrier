<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CompanyType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('nom', TextType::class)
            ->add('secteurActivite', TextType::class)
            ->add('raisonSociale', TextType::class)
            ->add('siegeSocial', TextType::class)
            ->add('email', TextType::class)
            ->add('telephone', TextType::class)
            ->add('directeur', TextType::class)
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\Company'
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mails_mailbundle_company';
    }
}
