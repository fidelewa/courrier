<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class MailReceivedHeir2Type extends AbstractType
{
    private $adminCompany;

    /**
     * @param string $class The User class name
     */
    public function __construct($adminCompany)
    {
        $this->adminCompany = $adminCompany;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
         ->add('user', 'entity', array(
        'class'    => 'MailsUserBundle:User',
        'choice_label' => 'username',
        'multiple' => false,
        'expanded' => false,
        'query_builder' => function (EntityRepository $er) {
            return $er->createQueryBuilder('u')
                ->where('u.roles = :role AND u.company = :company')
                //->setParameter('role', 'a:1:{i:0;s:15:"ROLE_SECRETAIRE";}')
                ->setParameters(array('role' => 'a:1:{i:0;s:15:"ROLE_SECRETAIRE";}', 'company' => $this->adminCompany));
        },
         ))
        ;
    }

    public function getName()
    {
        return 'mails_mailreceived_heir_2';
    }

    public function getParent()
    {
        return new MailReceivedType($this->adminCompany);
    }
}
