<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Doctrine\ORM\EntityRepository;

class MailsentRemovedateEnvoiAndSecretaryType extends AbstractType
{
    private $admin;

    /**
     * @param string $class The User class name
     */
    public function __construct($user)
    {
        $this->admin = $user;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->remove('dateEnvoi', 'datetime')//On supprime la date de reception
        ->remove('user', 'entity', array(//On supprime le champs de la sécrétaire qui doit enregistrer le courrier reçu
            'class'    => 'MailsUserBundle:User',
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->where('u.roles = :role AND u.company = :company')
                ->setParameters(array('role' => 'a:1:{i:0;s:15:"ROLE_SECRETAIRE";}', 'company' => $this->admin->getCompany()));
            },
            ))
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailsent_remove_secretary';
    }

    public function getParent()
    {
        return new MailSentType($this->admin->getCompany());
    }
}
