<?php

namespace Tests\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Security\Core\Authentication\Token\UsernamePasswordToken;
use Symfony\Component\BrowserKit\Cookie;

class ContactControllerTest extends WebTestCase
{

    protected function createAuthorizedClient()
    {
        $client = static::createClient();
        $container = $client->getContainer();
        $session = $container->get('session');
        
        $admin = self::$kernel->getContainer()->get('doctrine')->getRepository('UserBundle:User')->findOneByUsername('fiderlet');

        //$secretary = self::$kernel->getContainer()->get('doctrine')->getRepository('UserBundle:User')->findOneByUsername('alice');
        //$token = new UsernamePasswordToken($secretary, 'alice', 'main', $secretary->getRoles());

        $token = new UsernamePasswordToken($admin, 'fiderlet', 'main', $admin->getRoles());
        $session->set('_security_main', serialize($token));
        $session->save();

        $client->getCookieJar()->set(new Cookie($session->getName(), $session->getId()));

        return $client;
    }

    public function testShowAllInterlocutor()
    {
        $client = $this->createAuthorizedClient();

        $crawler = $client->request('GET', '/contact/show');

        $createNewContact = $crawler->selectLink('Créer un nouveau contact')->link();
        
        $crawler = $client->click($createNewContact);
    }

    public function testEditInterlocutor()
    {
        $client = $this->createAuthorizedClient();

        $crawler = $client->request('GET', '/contact/edit/1');

        $backContactList = $crawler->selectLink('Retour à la liste des contacts')->link();
        $accueil = $crawler->selectLink('Accueil')->link();
        
        $crawler = $client->click($backContactList);
        $crawler = $client->click($accueil);
    }

    public function testAddInterlocutor()
    {
        $client = $this->createAuthorizedClient();

        $crawler = $client->request('GET', '/contact/add');

        $backContactList = $crawler->selectLink('Retour à la liste des contacts')->link();
        $accueil = $crawler->selectLink('Accueil')->link();
        
        $crawler = $client->click($backContactList);
        $crawler = $client->click($accueil);
    }

    public function testShowAllMailInterlocutor()
    {
        $client = $this->createAuthorizedClient();

        $crawler = $client->request('GET', '/contact/all/mail/1');

        $mailSent = $crawler->selectLink('Courriers Envoyés')->link();
        $mailReceived = $crawler->selectLink('Courrier Reçus')->link();
        $newMailSent = $crawler->selectLink('Créer un nouveau courrier envoyé')->link();
        $filterMailSent = $crawler->selectLink('Filtrer les courriers envoyés par Roger')->link();
        $newMailReceived = $crawler->selectLink('Créer un nouveau courrier reçu')->link();
        $filterMailReceived = $crawler->selectLink('Filtrer les courriers reçus par Roger')->link();
        $carr2 = $crawler->selectLink('[CARR0002]')->link();
        $carr1 = $crawler->selectLink('[CARR0001]')->link();
        $cdep2 = $crawler->selectLink('[CDEP0002]')->link();
        $cdep1 = $crawler->selectLink('[CDEP0001]')->link();
        
        $crawler = $client->click($mailSent);
        $crawler = $client->click($mailReceived);
        $crawler = $client->click($newMailSent);
        $crawler = $client->click($filterMailSent);
        $crawler = $client->click($newMailReceived);
        $crawler = $client->click($filterMailReceived);
        $crawler = $client->click($carr2);
        $crawler = $client->click($carr1);
        $crawler = $client->click($cdep2);
        $crawler = $client->click($cdep1);
    }

}