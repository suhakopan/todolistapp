<?php

namespace App\DataFixtures;

use App\Entity\Developer;
use App\Entity\Provider;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        //adding developers on database
        foreach (range(1,5) as $index => $i) {
            $developer = new Developer();
            $developer
                ->setName('DEV$i')
                ->setHour(1)
                ->setExperience($i);
            $manager->persist($developer);
        }
        $manager->flush();
    }
}
