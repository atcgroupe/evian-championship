<?php

namespace App\DataFixtures;

use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customer = new User();
        $customer->setFirstName('Customer');
        $customer->setLastName('Customer');
        $customer->setEmail('informatique@atc-groupe.com');
        $customer->setPassword('$2y$13$sljKu8z5iZDq0NPeSDiUt.hh0rkUe563OvSPgDvlTqOn2LDPLq/AK');
        $customer->setRoles([User::ROLE_CUSTOMER]);
        $manager->persist($customer);

        $projectManager = new User();
        $projectManager->setFirstName('Anaïs');
        $projectManager->setLastName('Duparc');
        $projectManager->setEmail('anais.duparc@atc-groupe.com');
        $projectManager->setPassword('$2y$13$xX/fp/WatQyTcxe9M7wy2.dCCBe920lsA.QV4U5Man.TnNfaQvxzq');
        $projectManager->setRoles([User::ROLE_PROJECT_MANAGER]);
        $manager->persist($projectManager);

        $graphicDesigner = new User();
        $graphicDesigner->setFirstName('Laura');
        $graphicDesigner->setLastName('Poëzévara');
        $graphicDesigner->setEmail('laura.poezevara@atc-groupe.com');
        $graphicDesigner->setPassword('$2y$13$NeG7gqamiQ3kjY79GUGczeo4CqVo0e/fnCNKeC5lYZDgnERzGOtzm');
        $graphicDesigner->setRoles([User::ROLE_GRAPHIC_DESIGNER]);
        $manager->persist($graphicDesigner);

        $shippingManager = new User();
        $shippingManager->setFirstName('Nathalie');
        $shippingManager->setLastName('Florio');
        $shippingManager->setEmail('nathalie.florio@atc-groupe.com');
        $shippingManager->setPassword('$2y$13$sseN.BFWBNFfcOrqdom.jOudUieokuPQC2SCHsgv3Xkiy1fYGK0zm');
        $shippingManager->setRoles([User::ROLE_SHIPPING_MANAGER]);
        $manager->persist($shippingManager);

        $admin = new User();
        $admin->setFirstName('Pierre');
        $admin->setLastName('Gaimard');
        $admin->setEmail('pierre.gaimard@atc-groupe.com');
        $admin->setPassword('$2y$13$SxBNPPDnoiAC4LDn6iC1U.0myw.qzh4aeyBz8JkSJoDzUMVgZI5xO');
        $admin->setRoles([User::ROLE_ADMIN]);
        $manager->persist($admin);

        $manager->flush();
    }
}
