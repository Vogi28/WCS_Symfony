<?php
namespace App\DataFixtures;

use App\Entity\Season;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class SeasonFixtures extends Fixture implements DependentFixtureInterface
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Faker\Factory::create('fr_FR');
        for($i = 0; $i < 10; $i++)
        {
            $season = new Season();
            $season->setYear($this->faker->numberBetween(2005, 2019))
                    ->setDescription($this->faker->realText(200))
                    ->setProgram($this->getReference('program_'.$this->faker->numberBetween(0, 5)));
            $manager->persist($season);
            $this->addReference('season_' . $i, $season);
        }

        $manager->flush();

    }

    public function getDependencies()  
    {
      return [ProgramFixtures::class];
    }
}