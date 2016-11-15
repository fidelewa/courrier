<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class MailReceivedHeirType extends AbstractType
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
         ->remove('dateReception', 'datetime')
        ;
    }

    public function getName()
    {
        return 'mails_mailreceived_heir';
    }

    public function getParent()
    {
        return new MailReceivedType($this->adminCompany);
    }
}
