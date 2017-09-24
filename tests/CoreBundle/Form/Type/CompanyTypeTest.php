<?php

namespace Tests\CoreBundle\Form\Type;

use Mails\MailBundle\Form\Type\CompanyType;
use Mails\MailBundle\Entity\Company;
use Symfony\Component\Form\Test\TypeTestCase;

class CompanyTypeTest extends TypeTestCase
{
    public function testSubmitValidData()
    {
        $formData = array(
            'nom' => 'ALPHATOU',
            'secteurActivite' => 'IT',
            'raisonSociale' => 'SARL',
            'siegeSocial' => 'YOPOUGON',
            'email' => 'alphatou@email.com',
            'telephone' => '02280768',
            'directeur' => 'CHEICK OMAR'
        );

        $form = $this->factory->create(CompanyType::class);

        $object = Company::fromArray($formData);

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
