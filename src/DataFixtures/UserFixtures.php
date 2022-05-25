<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Enum\UserRole;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class UserFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $customer = new User();
        $customer->setFirstName('Justine');
        $customer->setLastName('Buisson');
        $customer->setEmail('justine.buisson@danone.com');
        $customer->setPassword('$2y$13$3gOUJVfrIQK9a4QC/WBXFuD/7DPWCQoz29nnByRyqd6ln6jgMm6C6 ');
        $customer->setRoles([UserRole::CUSTOMER->getValue()]);
        $manager->persist($customer);

        $customer2 = new User();
        $customer2->setFirstName('Vincent');
        $customer2->setLastName('Ripoll');
        $customer2->setEmail('vincent@vincentripoll.fr');
        $customer2->setPassword('$2y$13$R0CU4R4WmBZEBkt1/6nGq.KA16WX5vN0QBZBLu2NUHEiWxhCLjJFC');
        $customer2->setRoles([UserRole::CUSTOMER->getValue()]);
        $manager->persist($customer2);

        $projectManager = new User();
        $projectManager->setFirstName('Anaïs');
        $projectManager->setLastName('Duparc');
        $projectManager->setEmail('anais.duparc@atc-groupe.com');
        $projectManager->setPassword('$2y$13$xX/fp/WatQyTcxe9M7wy2.dCCBe920lsA.QV4U5Man.TnNfaQvxzq');
        $projectManager->setRoles([UserRole::PROJECT_MANAGER->getValue()]);
        $manager->persist($projectManager);

        $graphicDesigner = new User();
        $graphicDesigner->setFirstName('Laura');
        $graphicDesigner->setLastName('Poëzévara');
        $graphicDesigner->setEmail('laura.poezevara@atc-groupe.com');
        $graphicDesigner->setPassword('$2y$13$NeG7gqamiQ3kjY79GUGczeo4CqVo0e/fnCNKeC5lYZDgnERzGOtzm');
        $graphicDesigner->setRoles([UserRole::GRAPHIC_DESIGNER->getValue()]);
        $manager->persist($graphicDesigner);

        $shippingManager = new User();
        $shippingManager->setFirstName('Nathalie');
        $shippingManager->setLastName('Florio');
        $shippingManager->setEmail('nathalie.florio@atc-groupe.com');
        $shippingManager->setPassword('$2y$13$sseN.BFWBNFfcOrqdom.jOudUieokuPQC2SCHsgv3Xkiy1fYGK0zm');
        $shippingManager->setRoles([UserRole::SHIPPING_MANAGER->getValue()]);
        $manager->persist($shippingManager);

        $admin = new User();
        $admin->setFirstName('Pierre');
        $admin->setLastName('Gaimard');
        $admin->setEmail('pierre.gaimard@atc-groupe.com');
        $admin->setPassword('$2y$13$SxBNPPDnoiAC4LDn6iC1U.0myw.qzh4aeyBz8JkSJoDzUMVgZI5xO');
        $admin->setRoles([UserRole::ADMIN->getValue()]);
        $manager->persist($admin);

        $manager->flush();
    }
}
