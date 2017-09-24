<?php
namespace Mails\MailBundle\Alpha;

use Symfony\Component\HttpFoundation\Response;

class AlphaHTML
{
    // Méthode pour ajouter le « alpha » à une réponse
  public function displayAlpha(Response $response, $remainingDays)
  {
      $content = $response->getContent();

    // Code à rajouter
    $html = '<span style="color: red; font-size: 0.5em;"> - Alpha J-'.(int) $remainingDays.' !</span>';

    // Insertion du code dans la page, dans le premier <h2>
    $content = preg_replace(
      '#<h2>(.*?)</h2>#iU',
      '<h2>$1'.$html.'</h2>',
      $content,
      1
    );

    // Modification du contenu dans la réponse
    $response->setContent($content);

      return $response;
  }
}
