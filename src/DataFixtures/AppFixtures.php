<?php

namespace App\DataFixtures;

use App\Entity\Customer;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

/**
 * Class AppFixtures
 *
 * @package App\DataFixtures
 */
class AppFixtures extends Fixture
{
    /**
     * Example data.
     *
     * @param ObjectManager $manager
     */
    public function load(ObjectManager $manager)
    {
        for ($i = 0; $i < 50; $i++) {
            $customer = new Customer();
            $customer->setFirstName('test' . $i);
            $customer->setLastName('$faker->lastName' . $i);
            $customer->setEmail('faker'. $i . '@gmail.com');
            $customer->setPhoneNumber('01924502236');
            $manager->persist($customer);
        }

        $manager->flush();
    }
}
