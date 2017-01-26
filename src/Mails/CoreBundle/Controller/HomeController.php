<?php

namespace Mails\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Mails\CoreBundle\Form\Type\ContactType;
use Mails\MailBundle\Form\Type\CompanyType;
use Mails\MailBundle\Entity\Company;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class HomeController extends Controller
{
    public function indexAction()
    {
        return $this->render('MailsCoreBundle:Home:index.html.twig');
    }
    
    public function contactAction(Request $request)
    {
        //On crée notre formulaire de contact
        $form = $this->createForm(new ContactType());

        // Check the method
        if ($form->handleRequest($request)->isValid()) {
            // Bind value with form

            $data = $form->getData();

            $message = \Swift_Message::newInstance()
                ->setContentType('text/html')
                ->setSubject($data['subject'])
                ->setFrom($data['email'])
                ->setTo('fiderlet07@gmail.com')
                ->setBody($data['content']);

            $this->get('mailer')->send($message);

            // Launch the message flash
            $request
            ->getSession()
            ->getFlashBag()
            ->add('info', 'Merci de nous contacter, nous répondrons à vos questions dans les plus brefs délais.');

            // On redirige vers l'accueil
            return $this->redirect($this->generateUrl('mails_core_home'));
        }

         // Si la requête est en GET
        return $this->render('MailsCoreBundle:Home:contact.html.twig', array(
        'contact'   => $form->createView()
        ));
    }

    /**
     * Manage all mails
     * @param String $editRoute the name of the edition route
     * @param String $detailRoute the name of the detail route
     * @param Integer $id id number
     * @Security("has_role('ROLE_ADMIN')")
     */
    public function manageMailsAction($editRoute, $detailRoute, $deleteRoute, $id, $var, $type)
    {
        return $this->render('MailsCoreBundle:Home:manage_mails.html.twig', array(
            'editRoute' => $editRoute,
            'detailRoute' => $detailRoute,
            'deleteRoute' => $deleteRoute,
            'id' => $id,
            'var' => $var,
            'type' => $type,
        ));
    }

     /**
      * Create a company action.
      *
      * @param Request $request Incoming request
      * @Security("has_role('ROLE_ADMIN')")
      */
    public function createCompanyAction(Request $request)
    {
        // Création d'une entreprise
        $company = new Company();

        //On crée le formulaire de création de l'entreprise
        $form = $this->createForm(new CompanyType(), $company);

        // Si la requête est en POST
        if ($form->handleRequest($request)->isValid()) {

            // We define the id of the company in the user (super admin)
            $superAdmin = $this->getUser();
            $superAdmin->setCompany($company);

            $em = $this->getDoctrine()->getManager();
            $em->persist($company);
            $em->persist($superAdmin);
            $em->flush();

            $request
                    ->getSession()
                    ->getFlashBag()
                    ->add('success', 'Votre entreprise "'.$company->getNom().'" à bien été créée et enregistré.');

            // On rédirige vers la page des informations concernant l'entreprise
            return $this->redirect($this->generateUrl('mails_core_company_infos', array('id' => $company->getId())));
        }

          // Si la requête est en GET
        return $this->render('MailsCoreBundle:Home:company_create.html.twig', array(
        'companyForm' => $form->createView(),
        'title' => 'Ajouter les informations de votre entreprise'
        ));
    }

    /**
      * show informations about the specified company
      *
      * @param Integer $id Company id
      * @Security("has_role('ROLE_ADMIN')")
      */
    public function infosCompanyAction($id)
    {
        // On récupère l'EntityManager
          $em = $this->getDoctrine()->getManager();
          
          // On récupère l'entreprise spécifiée
          $company = $em
          ->getRepository('MailsMailBundle:Company')
          ->find($id)
          ;

        if (null === $company) {
            throw $this->createNotFoundException("L'entreprise' d'id ".$id." n'existe pas.");
        }

        return $this->render('MailsCoreBundle:Home:company_infos.html.twig', array(
          'company' => $company,
          'title' => 'Récapitulatif des informations de votre entreprise'
          ));
    }
}
