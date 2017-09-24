<?php

namespace Mails\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * User
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mails\UserBundle\Entity\UserRepository")
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
    * @ORM\ManyToOne(targetEntity="Mails\MailBundle\Entity\Company", inversedBy="users")
    * @ORM\JoinColumn(nullable=true)
    */
    private $company;

    /**
     * Set company
     *
     * @param \Mails\MailBundle\Entity\Company $company
     *
     * @return User
     */
    public function setCompany(\Mails\MailBundle\Entity\Company $company)
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
