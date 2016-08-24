<?php

namespace Mails\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class MailsUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
