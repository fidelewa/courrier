<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\Mail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mails\MailBundle\Form\Type\MailMailsentAdminType;
use Mails\MailBundle\Form\Type\MailMailsentSecretaryType;
use Mails\MailBundle\Form\Type\MailMailsentEditType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class MailsentController extends Controller
{
			/**
			* Add or create a mail sent action.
			*
			* @param Request $request Incoming request
			* @Security("has_role('ROLE_ADMIN')")
			* @Template("@mailsent_form_views/mailsent_add.html.twig")
			*/
			public function addMailsentAction(Request $request)
			{
				  // On récupère notre mail factory
        	$mailFactory = $this->get('mails_mail.mail_factory');
	
					//On crée le courrier envoyé
					$mailsent = $mailFactory::createMailSent();
					
					//On défini la date d'envoi du courrier envoyé à la date courante
					$mailsent->setdateEnvoi(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));

					//On crée le courrier
					$courier = $mailFactory::create();
					
					//On défini le courrier envoyé
					$courier->setMailsent($mailsent);

					//On crée notre formulaire
					$form = $this->createForm(new MailMailsentAdminType(), $courier);
					
					// Si la requête est en POST
					if($form->handleRequest($request)->isValid()) 
					{
							// On récupère notre service mail creator
          		$mailCreator = $this->get('mails_mail.mail_creator');	

							// On renvoi le conrrier envoyé crée
              $mail = $mailCreator->processCreateMailSent($form, $mailsent, $this->getUser());
							
							return $this->redirect($this->generateUrl('mails_mailsent_detail', array('id' => $mail->getId())));
					}
					// Si la requête est en GET
					return array('form' => $form->createView());	
			}

			/**
			* Edit a mail sent.
			*
			* @param integer $id Mail sent id
			* @param Request $request Incoming request
			* @Security("has_role('ROLE_ADMIN')")
			*/
			public function editMailsentAction($id, Request $request)
			{
					$em = $this->getDoctrine()->getManager();

					// On récupère le mail sent d'id $id en BDD
					$mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

					if (null === $mail) {
					throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}

					//On crée le formulaire
					$form = $this->createForm(new MailMailsentEditType(), $mail);

					//Si la requête est en POST 
					if($form->handleRequest($request)->isValid()) 
					{
							// Inutile de persister ici, Doctrine connait déja notre courrier envoyé
							$em->flush();

							$request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$mail->getReference().'" a bien été modifiée.');

							return $this->redirect($this->generateUrl('mails_user_mailsent'));
					}

					//Si la requête est en GET
					return $this->render('@mailsent_form_views/mailsent_edit.html.twig', array(
					'form'   => $form->createView(),
					'mail' => $mail // Je passe également le courrier envoyé a la vue si jamais elle veut l'afficher
					));
			}

			/**
			* Delete a mail sent.
			*
			* @param integer $id mail sent id
			* @param Request $request Incoming request
			* @Security("has_role('ROLE_ADMIN')")
			* @Template("@delete_mails_views/delete_mailsent.html.twig")
			*/
			public function deleteMailsentAction($id, Request $request)
			{
					$em = $this->getDoctrine()->getManager();

					// On récupère le mail sent d'id $id
					$mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

					if (null === $mail) {
					throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}

					// On crée un formulaire vide, qui ne contiendra que le champ CSRF
					// Cela permet de protéger la suppression d'annonce contre cette faille
					$form = $this->createFormBuilder()->getForm();
					
					// Si la requête est en POST, l'annonce sera supprimée
					if($form->handleRequest($request)->isValid())
					{
							//On stocke la référence du courrier envoyé dans une varable tampon
							$tempMailsentRef = $mail->getReference();
						
							// On supprime notre objet $mail dans la base de données
							$em->remove($mail);
							$em->flush();

							$request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$tempMailsentRef.'" a bien été supprimé.');
							
							//On détruit la variable tampon.
							unset($tempMailsentRef);

							// Puis on redirige vers l'accueil
							return $this->redirect($this->generateUrl('mails_core_home'));
					}
					// Si la requête est en GET, on affiche une page de confirmation avant de supprimer
					return array('mail' => $mail, 'form' => $form->createView());
			}

			/**
			* Register a mail sent.
			*
			* @param Request $request Incoming request
			* @param Integer $id mail sent id
			* @Security("has_role('ROLE_SECRETAIRE')")
			* @Template("@mailsent_form_views/mailsent_registred.html.twig")
			*/
			public function registerMailsentAction($id, Request $request)
			{
					//On récupère notre Entity Manager 
					$em = $this->getDoctrine()->getManager();

					// On récupère l'$id du mail sent 
					$mailSent = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

					if (null === $mailSent) {
					throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}
					
					//On défini la date d'enregistrement du courrier envoyé selon la date courante
					$mailSent->setdateEdition(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
					
					//On crée le formulaire
					$form = $this->createForm(new MailMailsentSecretaryType, $mailSent);
					
					//Si la réquête est en POST
					if($form->handleRequest($request)->isValid()) 
					{
							//On enregistre le mail sent
							$mailSent->setRegistred(true);

							//On enregistre le mail sent dans la BDD
							$em->persist($mailSent);
							$em->flush();

							//On redirige vers la page d'accueil
							$request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$mailSent->getReference().'" a bien été enregistré.');

							return $this->redirect($this->generateUrl('mails_core_home'));
					}
					//Si la réquête est en GET
					return array('form' => $form->createView());
			}

			/**
			* view the features of the mail sent
			*
			* @param Integer $id Mailsent id
			*/
			public function viewMailsentAction($id)
			{
					//On récupère l'EntityManager
					$em = $this->getDoctrine()->getManager();
					
					// Pour récupérer un courrier envoyé unique 
					$mail = $em
					->getRepository('MailsMailBundle:Mail')
					->findMailSent($id)
					;

					if (null === $mail) {
					throw $this->createNotFoundException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}

					return $this->render('MailsMailBundle:Mail:view_mailsent.html.twig', array(
					'mail' => $mail
					));
			}
}
