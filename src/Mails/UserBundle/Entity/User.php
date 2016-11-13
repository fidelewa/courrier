<?php

namespace Mails\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity
 */
class User extends BaseUser
{
    /**
   * @ORM\Column(name="id", type="integer")
   * @ORM\Id
   * @ORM\GeneratedValue(strategy="AUTO")
   */
    protected $id;

    /**
    * @ORM\ManyToOne(targetEntity="Mails\MailBundle\Entity\Company")
    * @ORM\JoinColumn(nullable=true)
    * @Assert\Valid
    */
    private $company;

    /**
     * Set company
     *
     * @param \Mails\MailBundle\Entity\Company $company
     *
     * @return User
     */
    public function setCompany(\Mails\MailBundle\Entity\Company $company = null)
    {
        $this->company = $company;

        return $this;
    }

    /**
     * Get company
     *
     * @return \Mails\MailBundle\Entity\Company
     */
    public function getCompany()
    {
        return $this->company;
    }
}
