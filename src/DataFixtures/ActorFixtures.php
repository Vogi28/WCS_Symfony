<?php
namespace App\DataFixtures;

use App\Entity\Actor;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class ActorFixtures extends Fixture implements DependentFixtureInterface
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Faker\Factory::create('en_US');
        for($i = 0; $i < 50; $i++)
        {
            $actor = new Actor();
            $actor->setName($this->faker->name)
                    ->addProgram($this->getReference('program_'.$this->faker->numberBetween(0, 5)));
            $manager->persist($actor);
            $this->addReference('actor_' . $i, $actor);
        }

        $manager->flush();

    }

    public function getDependencies()  
    {
      return [ProgramFixtures::class];
    }
}