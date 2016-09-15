<?php
namespace Mails\MailBundle\Checker;

class MailsChecker extends \Twig_Extension
{
    private $em;

    public function __construct(\Doctrine\ORM\EntityManager $entityManager)
    {
        $this->em = $entityManager;
    }
    
    public function checkReference($reference)
    {
        //On récupère un courrier par sa référence
        $findOneMailByReference = $this
                             ->em
                             ->getRepository('MailsMailBundle:Mail')
                             ->findByReference($reference)
                            ;
                            
        // Et on n'oublie pas de faire un flush !
        $this->em->flush();
        
        if(count($findOneMailByReference)>=1)
        {
          return "1";
        }
        else
        {
          return "2";
        }
        
    }

    // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
    return array(
      'checkRef' => new \Twig_Function_Method($this, 'checkReference')
    );
  }

  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
    return 'MailsChecker';
  }

}
