<?php
namespace App\DataFixtures;

use App\Entity\Episode;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Faker;

class EpisodeFixtures extends Fixture implements DependentFixtureInterface
{
    protected $faker;

    public function load(ObjectManager $manager)
    {
        $this->faker = Faker\Factory::create('fr_FR');
        for($i = 0; $i < 5; $i++)
        {
            $episode = new episode();
            $episode->setTitle($this->faker->realText(15))
                    ->setNumber($i)
                    ->setSynopsis($this->faker->realText(150))
                    ->setSeason($this->getReference('season_'.$this->faker->numberBetween(0, 9)));
            $manager->persist($episode);
            $this->addReference('episode_' . $i, $episode);
        }

        $manager->flush();

    }

    public function getDependencies()  
    {
      return [SeasonFixtures::class];
    }
}