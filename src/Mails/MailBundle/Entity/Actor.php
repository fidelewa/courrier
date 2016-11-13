<?php

namespace Mails\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Actor
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mails\MailBundle\Entity\ActorRepository")
 * @UniqueEntity(fields="name", message="Un contact existe déjà avec ce nom.")
 */
class Actor
{
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
    * @ORM\ManyToOne(targetEntity="Mails\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=true)
    * @Assert\Valid
    */
    private $user;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=20)
     */
    private $name;
    

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return Actor
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param \Mails\UserBundle\Entity\User $user
     *
     * @return Actor
     */
    public function setUser(\Mails\UserBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Mails\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
