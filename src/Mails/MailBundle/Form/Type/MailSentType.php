<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MailSentType extends AbstractType
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
            ->add('dateEnvoi', 'datetime')
            ->add('actor', 'entity', array(
            'class'    => 'MailsMailBundle:Actor',
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                ->join('a.user', 'u')
                ->addSelect('u')
                ->where('u.company = :company')
                ->setParameter('company', $this->adminCompany)
                ;
            },
            ))
            ->remove('user', 'entity', array(
            'class'    => 'MailsUserBundle:User',
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
            ))
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\MailSent'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'mails_mailbundle_mailsent';
    }
}
