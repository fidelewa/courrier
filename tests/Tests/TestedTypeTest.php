<?php
/**
 * Created by PhpStorm.
 * User: fidele
 * Date: 15/09/2017
 * Time: 15:58
 */

namespace Mails\Tests;


use Mails\MailBundle\Form\Type\ActorType;
use Mails\MailBundle\Entity\Actor;
use Symfony\Component\Form\Test\TypeTestCase;
use Mails\UserBundle\Entity\User;

class TestedTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $user = new User();

        $user->setUsername('fiderlet');

        $formData = array(
            'name' => 'makao',
            'user' => $user
        );

        $form = $this->factory->create(ActorType::class);

        $actor = new Actor();

        if (isset($formData['name'])) {
            $actor->setName($formData['name']);
        }
        if (isset($formData['user'])) {
            $actor->setUser($formData['user']);
        }

        $actor->setUser($formData['user']);

        $object = $actor;

        // submit the data to the form directly
        $form->submit($formData);

        $this->assertTrue($form->isSynchronized());
        $this->assertEquals($object, $form->getData());

        $view = $form->createView();
        $children = $view->children;

        foreach (array_keys($formData) as $key) {
            $this->assertArrayHasKey($key, $children);
        }
    }
}