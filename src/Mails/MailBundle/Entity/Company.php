<?php

namespace Mails\MailBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Company
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Mails\MailBundle\Entity\CompanyRepository")
 */
class Company
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
     * @var string
     *
     * @ORM\Column(name="nom", type="string", length=50)
     */
    private $nom;

    /**
     * @var string
     *
     * @ORM\Column(name="secteur_activite", type="string", length=50)
     */
    private $secteurActivite;

    /**
     * @var string
     *
     * @ORM\Column(name="raison_sociale", type="string", length=20)
     */
    private $raisonSociale;

    /**
     * @var string
     *
     * @ORM\Column(name="siege_social", type="string", length=30)
     */
    private $siegeSocial;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=20)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="telephone", type="string", length=8)
     */
    private $telephone;

    /**
     * @var string
     *
     * @ORM\Column(name="directeur", type="string", length=50)
     */
    private $directeur;

    /**
    * @ORM\OneToMany(targetEntity="Mails\UserBundle\Entity\User", mappedBy="company")
    */
    private $users; // Notez le « s », une entreprise est liée à plusieurs utilisateurs


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
     * Set nom
     *
     * @param string $nom
     *
     * @return Company
     */
    public function setNom($nom)
    {
        $this->nom = $nom;

        return $this;
    }

    /**
     * Get nom
     *
     * @return string
     */
    public function getNom()
    {
        return $this->nom;
    }

    /**
     * Set secteurActivite
     *
     * @param string $secteurActivite
     *
     * @return Company
     */
    public function setSecteurActivite($secteurActivite)
    {
        $this->secteurActivite = $secteurActivite;

        return $this;
    }

    /**
     * Get secteurActivite
     *
     * @return string
     */
    public function getSecteurActivite()
    {
        return $this->secteurActivite;
    }

    /**
     * Set raisonSociale
     *
     * @param string $raisonSociale
     *
     * @return Company
     */
    public function setRaisonSociale($raisonSociale)
    {
        $this->raisonSociale = $raisonSociale;

        return $this;
    }

    /**
     * Get raisonSociale
     *
     * @return string
     */
    public function getRaisonSociale()
    {
        return $this->raisonSociale;
    }

    /**
     * Set siegeSocial
     *
     * @param string $siegeSocial
     *
     * @return Company
     */
    public function setSiegeSocial($siegeSocial)
    {
        $this->siegeSocial = $siegeSocial;

        return $this;
    }

    /**
     * Get siegeSocial
     *
     * @return string
     */
    public function getSiegeSocial()
    {
        return $this->siegeSocial;
    }

    /**
     * Set email
     *
     * @param string $email
     *
     * @return Company
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set telephone
     *
     * @param string $telephone
     *
     * @return Company
     */
    public function setTelephone($telephone)
    {
        $this->telephone = $telephone;

        return $this;
    }

    /**
     * Get telephone
     *
     * @return string
     */
    public function getTelephone()
    {
        return $this->telephone;
    }

    /**
     * Set directeur
     *
     * @param string $directeur
     *
     * @return Company
     */
    public function setDirecteur($directeur)
    {
        $this->directeur = $directeur;

        return $this;
    }

    /**
     * Get directeur
     *
     * @return string
     */
    public function getDirecteur()
    {
        return $this->directeur;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->users = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add user
     *
     * @param \Mails\UserBundle\Entity\User $user
     *
     * @return Company
     */
    public function addUser(\Mails\UserBundle\Entity\User $user)
    {
        //En liant les users à l'entreprise

        $this->users[] = $user;

        return $this;

        // On lie l'entreprise à l'user
        $user->setCompany($this);
        
        //Ici l'user courant retourné est $user

        return $this;//On retourne la company fraichement liée.
    }

    /**
     * Remove user
     *
     * @param \Mails\UserBundle\Entity\User $user
     */
    public function removeUser(\Mails\UserBundle\Entity\User $user)
    {
        $this->users->removeElement($user);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getUsers()
    {
        return $this->users;
    }
}
