<?php
namespace Mails\MailBundle\Manager;

class MailsManager extends \Twig_Extension
{
    private $template;

    public function __construct($templating)
    {
        $this->template = $templating;
    }

    public function manageMails($editRoute, $detailRoute, $deleteRoute, $id, $var, $type)
    {
        return $this->template->render('MailsCoreBundle:Home:manage_mails.html.twig', array(
            'editRoute' => $editRoute,
            'detailRoute' => $detailRoute,
            'deleteRoute' => $deleteRoute,
            'id' => $id,
            'var' => $var,
            'type' => $type,
        ));
    }

  // Twig va exécuter cette méthode pour savoir quelle(s) fonction(s) ajoute notre service
  public function getFunctions()
  {
      return array(
      'manageMails' => new \Twig_Function_Method($this, 'manageMails')
    );
  }

  // La méthode getName() identifie votre extension Twig, elle est obligatoire
  public function getName()
  {
      return 'MailsManager';
  }
}
