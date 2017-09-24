<?php

namespace Tests\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase
{
    public function testIndex()
    {
        $client = static::createClient();

        $crawler = $client->request('GET', '/');

        $listMails = self::$kernel->getContainer()->get('doctrine')->getRepository('MailsMailBundle:Mail')->findAll();

        foreach ($listMail as $mail) {
        }

        $voirPlus = $crawler->selectLink('Voir plus')->link();
        $connexion = $crawler->selectLink('Connexion')->link();
        $Deconnection = $crawler->selectLink('Déconnexion')->link();

        $crawler = $client->click($voirPlus);
        $crawler = $client->click($connexion);
        $crawler = $client->click($connexion);
    }

    public function testContact()
    {
        $client = static::createClient();

      // Directly submit a form
      $client->request('POST', '/contact', array(
        'email' => 'fiderlet07@gmail.com',
        'subject' => 'Message de test',
        'content' => 'C\'est un test'
        ));
    }

    public function testCreateCompany()
    {
        $client = static::createClient();

      // Directly submit a form
      $client->request('POST', '/company', array(
        'nom' => 'ALPHATOU',
        'secteurActivite' => 'IT',
        'raisonSociale' => 'SARL',
        'siegeSocial' => 'YOPOUGON',
        'email' => 'alphatou@email.com',
        'telephone' => '02280768',
        'directeur' => 'CHEICK OMAR'
        ));
    }
}
