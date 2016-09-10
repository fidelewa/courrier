<?php
// src/Mails/MailBundle/DataFixtures/ORM/LoadMail.php

namespace Mails\MailBundle\DataFixtures\ORM;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Mails\MailBundle\Entity\MailSent;
use Mails\MailBundle\Entity\MailReceived;
use Mails\MailBundle\Entity\Mail;
use Mails\UserBundle\Entity\User;
use Mails\MailBundle\Entity\Actor;

class LoadMail implements FixtureInterface
{
    // Dans l'argument de la méthode load, l'objet $manager est l'EntityManager
    public function load(ObjectManager $manager)
    {
          // CREATION DES USERS
          $listNames = array('JohnDoe', 'JaneDoe', 'Admin');
          
          //liste des users vide
          $users = [];

          foreach ($listNames as $name) {
            // On crée l'utilisateur
            $user = new User;

            // On défini le nom d'utilisateur, le mot de passe et l'adresse e-mail
            $user->setUsername($name);
            $user->setPassword($name);
            $user->setEmail($name);
            $user->setEnabled(true);

            // On ne se sert pas du sel pour l'instant
            // On définit uniquement le role ROLE_USER qui est le role de base
            $user->setRoles(array('ROLE_USER','ROLE_ADMIN'));
            
            // On le persiste
            $manager->persist($user);
            
            //On défini la liste des users
            $users[] = $user;
          }

           //---------------------------------------------------------------

          //CREATION DES ACTEURS
            
          // Les noms des acteurs classic à créer
          $listNames = array('Fiderlet', 'Kingston', 'Logan');
          
          //liste des acteurs vide
          $actors = [];

          foreach ($listNames as $name) {
            // On crée l'acteur classic
            $actor = new Actor();
            
            // On défini ses attributs
            $actor->setname($name);
            
            // On le persiste
            $manager->persist($actor);
            
            //On défini la liste des acteurs
            $actors[] = $actor;
          }
          
          //On fusionne toutes les listes
          $allActors = array_merge($users,$actors);
          
          //On extrait chaque élément de la liste fusionnée  
          extract($allActors, EXTR_PREFIX_INVALID, "acteur");
          //echo $acteur_0, $acteur_1, $acteur_2, $acteur_3, $acteur_4, $acteur_5;
          
          //------------------------------------------------------------------------
            
            //COURRIER ENVOYES
            
            //MAIL SENT 1
            $mailsent1 = new MailSent(['user'=> $acteur_0,
                                       'actor'=> $acteur_3,
                                      ]
                                    );
            $manager->persist($mailsent1);
            
            //MAIL 1
            $mail1 = new Mail(['reference'          => 'CDEP0001',
                                'objet'               => 'Welcome',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 1,
                                'received'            => false, 
                                'mailsent'            => $mailsent1, 
                              ]
                            );
            $manager->persist($mail1);
            
            //----------------------------------------------------
            
            //MAIL SENT 2
            $mailsent2 = new MailSent(['user'=> $acteur_1,
                                       'actor'=> $acteur_4,
                                      ]
                                    );
            $manager->persist($mailsent2);
            
            //MAIL 2
            $mail2 = new Mail(['reference'          => 'CDEP0002',
                                'objet'               => 'Good Evening',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 3,
                                'received'            => true, 
                                'mailsent'            => $mailsent2, 
                              ]
                            );
            $manager->persist($mail2);
            
            //--------------------------------------------------
            
            //MAIL SENT 3
            $mailsent3 = new MailSent(['user'=> $acteur_2,
                                       'actor'=> $acteur_5,
                                      ]
                                    );
            $manager->persist($mailsent3);
            
            //MAIL 3
            $mail3 = new Mail(['reference'          => 'CDEP0003',
                                'objet'               => 'Tired',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 0,
                                'received'            => false, 
                                'mailsent'            => $mailsent3, 
                              ]
                            );
            $manager->persist($mail3);

            // COURRIER RECUS
            
            //MAIL RECEIVED 1
            $mailreceived1 = new MailReceived(['user'=> $acteur_0,
                                               'actor'=> $acteur_3,
                                               'treated'             => true,
                                              ]
                                            );
            $manager->persist($mailreceived1);
            
            //MAIL 4
            $mail4 = new Mail(['reference'          => 'CARR0001',
                                'objet'               => 'Bonjour tout le monde',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 8,
                                'received'            => false, 
                                'mailreceived'            => $mailreceived1, 
                              ]
                            );
            $manager->persist($mail4);
            
            //-----------------------------------------------------------------------
            
            //MAIL RECEIVED 2
            $mailreceived2 = new MailReceived(['user'=> $acteur_1,
                                               'actor'=> $acteur_4,
                                               'treated'             => false,
                                              ]
                                            );
            $manager->persist($mailreceived2);
            
            //MAIL 5
            $mail5 = new Mail(['reference'          => 'CARR0002',
                                'objet'               => 'Ouais c\'est bon',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 6,
                                'received'            => true, 
                                'mailreceived'            => $mailreceived2, 
                              ]
                            );
            $manager->persist($mail5);
            
            //-----------------------------------------------------
            
            //MAIL RECEIVED 3
            $mailreceived3 = new MailReceived(['user'=> $acteur_2,
                                               'actor'=> $acteur_5,
                                               'treated'             => false,
                                              ]
                                            );
            $manager->persist($mailreceived3);
            
            //MAIL 6
            $mail6 = new Mail(['reference'          => 'CARR0003',
                                'objet'               => 'Quelle chaleur!',
                                //'dateEdition'         => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                //'dateEnvoi'           => new \Datetime("now", new \DateTimeZone('Africa/Abidjan')),
                                'nombrePiecesJointes' => 6,
                                'received'            => true, 
                                'mailreceived'            => $mailreceived3, 
                              ]
                            );
            $manager->persist($mail6);
            
            
            //-------------------------------------------------------------
            
            // On exécute tous les persist
            $manager->flush();  
    }
}