<?php
// src/OC/PlatformBundle/alpha/alphaListener.php

namespace Mails\MailBundle\Alpha;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

class AlphaListener
{
    // Notre processeur
    protected $alphaHTML;

  // La date de fin de la version alpha :
  // - Avant cette date, on affichera un compte à rebours (J-3 par exemple)
  // - Après cette date, on n'affichera plus le « alpha »
    protected $endDate;

    public function __construct(alphaHTML $alphaHTML, $endDate)
    {
        $this->alphaHTML = $alphaHTML;
        $this->endDate  = new \Datetime($endDate);
    }

  // Methode permettant d'effetuer le traitement
  public function processAlpha(FilterResponseEvent $event)
  {
      // On teste si la requête est bien la requête principale (et non une sous-requête)
        if (!$event->isMasterRequest()) {
            return;
        }

      // Nombre de jours restants est égale à la différence entre la date de today et la date de fin.
          $remainingDays = $this->endDate->diff(new \Datetime())->format('%d');

      // Si la date est dépassée, on ne fait rien
        if ($remainingDays <= 0) {
            return;
        }

    // Ici on modifie comme on veut la réponse…

    // On utilise notre alphaHTML pour modifier la réponse que le gestionnaire a insérée dans l'évènement
        $response = $this->alphaHTML->displayAlpha($event->getResponse(), $remainingDays);

    // On met à jour la réponse avec la nouvelle valeur puis on insère la réponse modifiée dans l'évènement
        $event->setResponse($response);

    // On stoppe la propagation (diffusion ou déclenchement) de l'évènement en cours (ici, kernel.response)
    //$event->stopPropagation();
  }
}
