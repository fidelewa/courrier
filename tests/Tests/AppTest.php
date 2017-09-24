<?php

namespace Mails\Tests;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class AppTest extends WebTestCase
{
    protected function createAuthorizedClientRoleAdmin()
    {
        $client = static::createClient();
        $session = self::$kernel->getContainer()->get('session');

        $admin = self::$kernel->getContainer()->get('doctrine')->getRepository('UserBundle:User')->findOneByUsername('fiderlet');

        $token = new UsernamePasswordToken($admin, 'fiderlet', 'main', $admin->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    protected function createAuthorizedClientRoleSecretary()
    {
        $client = static::createClient();
        $session = self::$kernel->getContainer()->get('session');

        $secretary = self::$kernel->getContainer()->get('doctrine')->getRepository('UserBundle:User')->findOneByUsername('laura');


        $token = new UsernamePasswordToken($secretary, 'laura', 'main', $secretary->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }


    /**
     * @dataProvider urlProviderRoleAdmin
     */
    public function testPageIsSuccessfulRoleAdmin($url)
    {
        // Test des URLs

        $client = $this->createAuthorizedClientRoleAdmin();
        $client->followRedirects();
        $crawler = $client->request('GET', $url);
        $this->assertEquals(
            200,
            $client->getResponse()->getStatusCode()
        );
    }

    /**
     * @dataProvider urlProviderRoleSecretary
     */
     public function testPageIsSuccessfulRoleSecretary($url)
     {
         // Test des URLs
 
         $client = $this->createAuthorizedClientRoleSecretary();
         $client->followRedirects();
         $crawler = $client->request('GET', $url);
         $this->assertEquals(
             200,
             $client->getResponse()->getStatusCode()
         );
     }

    public function urlProviderRoleAdmin()
    {
        return array(
             array('/'),
            array('/contact'),
            array('/company'),
            array('/infos/company'),
            array('/workspace/admin'),
            array('/mail/sent/user/1'),
            array('/mail/received/user/1'),
            array('/mail/sent/1'),
            array('/mail/received/2'),
            array('/mail/mailreceived/add'),
            array('/mail/mailreceived/edit/2'),

            array('/mail/mailreceived/2'),
            array('/mail/mailsent/add'),
            array('/mail/mailsent/edit/1'),
            array('/mail/mailsent/1'),
            array('/contact/show'),
            array('/contact/edit/1'),
            array('/contact/add'),
            array('/contact/all/mail/1'),
            array('/user/show'),
            array('/user/mailsent/1'),
            array('/user/mailreceived/1'),
            array('/user/all/mail/1'),
            array('/mail/validate/mailsent/1'),//REDIRECTION
            array('/mail/validate/mailreceived/2'),//REDIRECTION

            array('/mail/mailreceived/filter'),
            //array('/mail/mailreceived/filter/result'),
            array('/mail/mailreceived/filter/user/1'),
            //array('/mail/mailreceived/filter/user/result'),
            array('/mail/mailreceived/filter/contact/1'),
            //array('/mail/mailreceived/filter/contact/result'),
            array('/mail/all/mailreceived/filter/1'), 
            //array('/mail/all/mailreceived/filter/result/1'), 
            array('/mail/mailsent/filter'),
            //array('/mail/mailsent/filter/result'),
            array('/mail/mailsent/filter/user/1'),
            //array('/mail/mailsent/filter/user/result'),
            array('/mail/mailsent/filter/contact/1'),
            //array('/mail/mailsent/filter/contact/result'),
            array('/mail/all/mailsent/filter/1'),
            //array('/mail/all/mailsent/filter/result/1'),
            array('/mail/all/mailreceived/filter/user'),
            //array('/mail/all/mailreceived/filter/user/result/1'),
            
            array('/mail/mailsent/delete/1'),
            array('/mail/mailreceived/delete/2'),
            array('/mail/mailsent/delete/1'),
            array('/contact/delete/1'),//REDIRECTION
            array('/user/delete/1'),//REDIRECTION
            
        );
    }

    public function urlProviderRoleSecretary()
    {
        return array(
            array('/workspace/secretary'),
            array('/mail/register/mailsent/1'),
            array('/mail/register/mailreceived/2'),
        );
    }
}