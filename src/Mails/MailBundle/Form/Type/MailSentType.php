<?php

namespace Mails\MailBundle\Form\Type;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class MailSentType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // On définit l'attribut de l'entreprise de l'utilisateur
        if (!isset($this->adminCompany)) {
            $this->adminCompany = $options['adminCompany'];
        }

        $builder
            ->add('dateEnvoi', DateTimeType::class)
            ->add('actor', EntityType::class, array(//Champs du destinataire du courrier envoyé
            'class'    => 'MailsMailBundle:Actor',
            'choice_label' => 'name',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('a')
                ->join('a.user', 'u')
                ->addSelect('u')
                ->where('u.company = :company')
                ->setParameter('company', $this->adminCompany)
                ;
            },
            ))
            ->add('user', EntityType::class, array(//Champs de la sécrétaire qui doit enregistrer le courrier envoyé
            'class'    => 'MailsUserBundle:User',
            'choice_label' => 'username',
            'multiple' => false,
            'expanded' => false,
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('u')
                ->where('u.roles = :role AND u.company = :company')
                ->setParameters(array('role' => 'a:1:{i:0;s:15:"ROLE_SECRETAIRE";}', 'company' => $this->adminCompany));
            },
          ))
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Mails\MailBundle\Entity\MailSent',
            'adminCompany' => null,
        ));
    }

    /**
     * @return string
     */
    public function getBlockPrefix()
    {
        return 'mails_mailbundle_mailsent';
    }
}
