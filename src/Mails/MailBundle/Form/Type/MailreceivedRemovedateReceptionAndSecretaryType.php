<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class MailreceivedRemovedateReceptionAndSecretaryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->adminCompany = $options['adminCompany'];

        $builder
        ->remove('dateReception', 'datetime')//On supprime la date de reception
        ->remove('user', 'entity', array(//On supprime le champs de la sécrétaire qui doit enregistrer le courrier reçu
            'class'    => 'UserBundle:User',
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

    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailreceived_remove_secretary';
    }

    public function getParent()
    {
        return MailReceivedType::class;
    }
}
