<?php

namespace Mails\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * MailSent
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mails\MailBundle\Entity\MailSentRepository")
 */
class MailSent 
{
   /**
    * @ORM\ManyToOne(targetEntity="Mails\MailBundle\Entity\Actor")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\Valid
    */
    private $actor;
    
   /**
    * @ORM\ManyToOne(targetEntity="Mails\UserBundle\Entity\User")
    * @ORM\JoinColumn(nullable=false)
    * @Assert\Valid
    */
    private $user;
    
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="date_envoi", type="datetime", nullable=true)
     * @Assert\DateTime()
     */
    private $dateEnvoi;
    
    /**
     * Set id
     *
     * @param integer $id
     * @return MailSent
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }


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
     * Set dateEnvoi
     *
     * @param \DateTime $dateEnvoi
     * @return MailSent
     */
    public function setDateEnvoi($dateEnvoi)
    {
        $this->dateEnvoi = $dateEnvoi;

        return $this;
    }

    /**
     * Get dateEnvoi
     *
     * @return \DateTime 
     */
    public function getDateEnvoi()
    {
        return $this->dateEnvoi;
    }



    /**
     * Set actor
     *
     * @param \Mails\MailBundle\Entity\Actor $actor
     * @return MailSent
     */
    public function setActor(Actor $actor)
    {
        $this->actor = $actor;

        return $this;
    }

    /**
     * Get actor
     *
     * @return \Mails\MailBundle\Entity\Actor 
     */
    public function getActor()
    {
        return $this->actor;
    }

    /**
     * Set user
     *
     * @param \Mails\UserBundle\Entity\User $user
     * @return MailSent
     */
    public function setUser(\Mails\UserBundle\Entity\User $user)
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
