<?php
namespace Mails\MailBundle\Factory;

class MailFactory
{
    public static function create()
    {
        return new \Mails\MailBundle\Entity\Mail();
    }

    public static function createMailSent()
    {
        return new \Mails\MailBundle\Entity\MailSent();
    }
    
    public static function createMailReceived()
    {
        return new \Mails\MailBundle\Entity\MailReceived();
    }
}
