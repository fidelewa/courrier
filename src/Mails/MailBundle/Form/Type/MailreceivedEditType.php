<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailreceivedEditType extends AbstractType
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
        ->remove('dateEdition', 'datetime')
        ->add('mailreceived', new MailreceivedRemoveSecretaryType($this->admin))
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailreceived_edit';
    }

    public function getParent()
    {
        return new MailMailreceivedType($this->admin->getCompany());
    }
}