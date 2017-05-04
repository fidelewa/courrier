<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class MailsentRemoveSecretaryType extends AbstractType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $this->adminCompany = $options['adminCompany'];

        $builder
        ->remove('user', 'entity', array(//Champs de la sécrétaire qui doit enregistrer le courrier envoyé
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
        return 'mails_mailbundle_mailsent_remove_secretary';
    }

    public function getParent()
    {
        return MailSentType::class;
    }
}
