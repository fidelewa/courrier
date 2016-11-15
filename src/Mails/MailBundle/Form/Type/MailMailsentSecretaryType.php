<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailMailsentSecretaryType extends AbstractType
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
            ->remove('mailsent')
            ->remove('nombrePiecesJointes')
        ;
    }

    public function getName()
    {
        return 'mails_mailbundle_mailsent_secretary';
    }

    public function getParent()
    {
        return new MailMailsentType($this->adminCompany);
    }
}
