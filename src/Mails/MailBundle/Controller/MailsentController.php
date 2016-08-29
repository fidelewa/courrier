<?php

namespace Mails\MailBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\Request;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\Mail;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Mails\MailBundle\Form\MailMailsentAdminType;
use Mails\MailBundle\Form\MailMailsentSecretaryType;
use Mails\MailBundle\Form\MailMailsentEditType;

class MailsentController extends Controller
{
			/**
			* Add or create a mail sent action.
			*
			* @param Request $request Incoming request
			* @Security("has_role('ROLE_ADMIN')")
			*/     
			public function addMailsentAction(Request $request)
			{
					//On récupère l'EntityManager
					$em = $this->getDoctrine()->getManager();
					
					//On crée le mail
					$mail = new Mail();
							
					//On crée le mail sent
					$mailsent = new MailSent();
					
					//On défini la date d'envoi du courrier envoyé à la date courante
					$mailsent->setdateEnvoi(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
					
					//On défini le mail sent
					$mail->setMailsent($mailsent);

					//On crée notre formulaire
					$form = $this->createForm(new MailMailsentAdminType(), $mail);
					
					// Si la requête est en POST
					if($form->handleRequest($request)->isValid()) 
					{
							//On récupère l'id de la sécrétaire
							$mail = $form->getData();
							$idSecretary = $mail->getMailsent()->getUser()->getId();
							
							//On récupère l'interlocuteur
							$actor = $mail->getMailsent()->getActor();
							
							//On défini l'interlocuteur
							$mailsent->setActor($actor);
							
							//On défini le visa de la sécrétaire
							$mail->setVisaSecretaire($idSecretary);
							
							//On défini l'administrateur
							$admin = $this->getUser();
							$mailsent->setUser($admin);
							
							//On défini le mail sent
							$mail->setMailsent($mailsent);
							
							//On enregiste le courrier en BDD
							$em->persist($mail);
							$em->flush();

							$request->getSession()->getFlashBag()->add('info', 'Le courrier envoyé de référence "'.$mail->getReference().'" à bien été crée.');
							
							return $this->redirect($this->generateUrl('mails_mailsent_detail', array('id' => $mail->getId())));
					}
					
					// On récupère notre service
					$checker = $this->get('mails_mail.mail_checker');

					//On récupère un courrier par sa référence
					$findOneMailByReference = $checker->checkReference('CDEP0001');
					
					// Si la requête est en GET
					return $this->render('MailsMailBundle:Mail:mailsent_add.html.twig', array(
					'form' => $form->createView(),
					'findOneMailByReference' => $findOneMailByReference,
					));
					
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

					// On récupère le mail sent d'id $id
					$mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

					if (null === $mail) {
					throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}
					
					//On récupère les attributs du mailsent existant en BDD
					$id = $mail->getMailsent()->getId(); 
					$actor = $mail->getMailsent()->getActor(); 
					$user = $mail->getMailsent()->getUser(); 
					$dateEnvoi = $mail->getMailsent()->getDateEnvoi();
					
					//On instancie un nouveau mail sent 
					$mailsent = new MailSent();
					
					//On met a jour ses attributs
					$mailsent->setId($id);
					$mailsent->setActor($actor);
					$mailsent->setUser($user);
					$mailsent->setDateEnvoi($dateEnvoi);

					//On défini le mail sent
					$mail->setMailsent($mailsent);

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
					return $this->render('MailsMailBundle:Mail:mailsent_edit.html.twig', array(
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
					
					if($form->handleRequest($request)->isValid()){
					// Si la requête est en POST, l'annonce sera supprimée
					
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
					return $this->render('MailsMailBundle:Mail:delete_mailsent.html.twig', array(
					'mail' => $mail,
					'form'   => $form->createView()
					));
	
			}

			/**
			* Register a mail sent.
			*
			* @param Request $request Incoming request
			* @param Integer $id mail sent id
			* @Security("has_role('ROLE_SECRETAIRE')")
			*/
			public function registerMailsentAction($id, Request $request)
			{
					//On récupère notre Entity Manager 
					$em = $this->getDoctrine()->getManager();

					// On récupère l'$id du mail sent 
					$mail = $em->getRepository('MailsMailBundle:Mail')->findMailSent($id);

					if (null === $mail) {
					throw new NotFoundHttpException("Le courrier envoyé d'id ".$id." n'existe pas.");
					}
					
					//On défini la date d'enregistrement du courrier envoyé selon la date courante
					$mail->setdateEdition(new \Datetime("now", new \DateTimeZone('Africa/Abidjan')));
					
					//On crée le formulaire
					$form = $this->createForm(new MailMailsentSecretaryType, $mail);
					
					//Si la réquête est en POST
					if($form->handleRequest($request)->isValid()) 
					{
							//On enregistre le mail sent
							$mail->setRegistred(true);

							//On enregistre le mail sent dans la BDD
							$em->persist($mail);
							$em->flush();

							//On redirige vers la page d'accueil
							$request->getSession()->getFlashBag()->add('success', 'Le courrier envoyé de référence "'.$mail->getReference().'" a bien été enregistré.');

							return $this->redirect($this->generateUrl('mails_core_home'));
					}
					
					//Si la réquête est en GET
					return $this->render('MailsMailBundle:Mail:mailsent_registred.html.twig', array(
					'form' => $form->createView(),
					));
						
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
