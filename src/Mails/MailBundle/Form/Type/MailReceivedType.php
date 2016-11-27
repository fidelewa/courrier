<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Doctrine\ORM\EntityRepository;

class MailReceivedType extends AbstractType
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
            ->add('dateReception', 'datetime')
            ->add('actor', 'entity', array(//Champs du destinataire du courrier reçu
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
            ->add('user', 'entity', array(//Champs de la sécrétaire qui doit enregistrer le courrier reçu
            'class'    => 'MailsUserBundle:User',
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->where('u.roles = :role AND u.company = :company')
                ->setParameters(array('role' => 'a:1:{i:0;s:15:"ROLE_SECRETAIRE";}', 'company' => $this->adminCompany));
            },
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
