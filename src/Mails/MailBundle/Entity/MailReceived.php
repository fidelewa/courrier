<?php

namespace Mails\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


/**
 * MailReceived
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mails\MailBundle\Entity\MailReceivedRepository")
 */
class MailReceived
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
     * @ORM\Column(name="date_reception", type="datetime")
     * @Assert\DateTime()
     */
    private $dateReception;
    
    public function __construct()
    {
        //Par défaut la date d'édition du courrier est la date d'aujourd'hui
        //$this->dateReception = new \Datetime("now", new \DateTimeZone('Africa/Abidjan'));
        //$this->hydrate($donnees);

    }
    
    /*public function hydrate(array $donnees)
    {
        foreach ($donnees as $key => $value) 
           {
              $method = 'set'.ucfirst($key);
                    
              if(method_exists($this,$method))
              {
                 $this->$method($value);
              }
           }
    }*/


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
     * Set dateReception
     *
     * @param \DateTime $dateReception
     * @return MailReceived
     */
    public function setDateReception($dateReception)
    {
        $this->dateReception = $dateReception;

        return $this;
    }

    /**
     * Get dateReception
     *
     * @return \DateTime 
     */
    public function getDateReception()
    {
        return $this->dateReception;
    }


    /**
     * Set actor
     *
     * @param \Mails\MailBundle\Entity\Actor $actor
     * @return MailReceived
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
     * @return MailReceived
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
