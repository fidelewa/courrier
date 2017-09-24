<?php

namespace Tests\MailBundle\Repository;

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class MailRepositoryTest extends KernelTestCase
{
    /**
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        self::bootKernel();

        $this->em = static::$kernel->getContainer()
            ->get('doctrine')
            ->getManager();
    }

    public function testfindByTreated()
    {
        $mailsTreated = $this->em
            ->getRepository('MailsMailBundle:Mail')
            ->findByTreated(true)
        ;

        $this->assertCount(2, $mailsTreated);
    }

    /**
     * {@inheritDoc}
     */
    protected function tearDown()
    {
        parent::tearDown();

        $this->em->close();
        $this->em = null; // avoid memory leaks
    }
}