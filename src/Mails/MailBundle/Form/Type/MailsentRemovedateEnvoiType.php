<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailsentRemovedateEnvoiType extends AbstractType
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
         ->remove('dateEnvoi', 'datetime')
        ;
    }

    public function getName()
    {
        return 'mails_mailsent_heir';
    }

    public function getParent()
    {
        return new MailSentType($this->adminCompany);
    }
}
