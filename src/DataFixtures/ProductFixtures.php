<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    private const NAME = 'name';
    private const PRICE = 'price';

    public function load(ObjectManager $manager)
    {
        $data = [
            [ self::NAME => 'DIBOND 3MM', self::PRICE => 41.50 ],
            [ self::NAME => 'FOREX 3MM', self::PRICE => 24.00 ],
            [ self::NAME => 'FOREX 5MM', self::PRICE => 28.00 ],
            [ self::NAME => 'AKILUX 10/12MM', self::PRICE => 41.00 ],
            [ self::NAME => 'TOILE SANS PVC', self::PRICE => 28.00 ],
            [ self::NAME => 'BACHE M1', self::PRICE => 16.50 ],
            [ self::NAME => 'BACHE OPAQUE 650G', self::PRICE => 19.80 ],
            [ self::NAME => 'SEMI-DÉCOUPE', self::PRICE => 40.00 ],
            [ self::NAME => 'ADHESIF ENLEVABLE', self::PRICE => 27.00 ],
            [ self::NAME => 'ADHESIF ENLEVABLE PLASTIFIÉ', self::PRICE => 35.00 ],
            [ self::NAME => 'ADHESIF MICROPERFORÉ [E9]', self::PRICE => 30.00 ],
            [ self::NAME => 'GRILLE SKYMESH', self::PRICE => 17.20 ],
            [ self::NAME => 'GRILLE VISTAFLEX', self::PRICE => 16.50 ],
        ];

        foreach ($data as $item) {
            $product = new Product();
            $product->setName($item[self::NAME]);
            $product->setPrice($item[self::PRICE]);
            $manager->persist($product);
        }

        $manager->flush();
    }
}
