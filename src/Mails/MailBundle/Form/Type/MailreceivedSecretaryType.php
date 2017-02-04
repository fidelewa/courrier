<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailreceivedSecretaryType extends AbstractType
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
            ->remove('reference')
            ->remove('objet')
            ->remove('mailreceived')
            ->remove('nombrePiecesJointes')
            ->add('enregistrer', 'submit')
            ->remove('save', 'submit')
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailreceived_secretary';
    }

    public function getParent()
    {
        return new MailMailreceivedType($this->adminCompany);
    }
}
