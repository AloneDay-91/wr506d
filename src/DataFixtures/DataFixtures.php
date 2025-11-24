<?php

namespace App\DataFixtures;

use App\Entity\Director;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Actor;
use App\Entity\Movie;
use App\Entity\Category;
use Faker\Factory;
use Xylis\FakerCinema\Provider\Person;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $imageUrl = 'https://placehold.co/600x400';
        $categoriesArray = [];

        $actorsArray = $this->loadActors($manager, $imageUrl);
        $directorsArray = $this->loadDirectors($manager);
        $this->loadMovies($manager, $actorsArray, $directorsArray, $categoriesArray, $imageUrl);

        $manager->flush();
    }

    private function loadActors(ObjectManager $manager, string $imageUrl): array
    {
        $faker = Factory::create();
        $faker->addProvider(new Person($faker));
        $actors = $faker->actors($gender = null, $count = 190, $duplicates = false);
        $actorsArray = [];

        foreach ($actors as $item) {
            $actor = new Actor();
            $names = explode(' ', $item);

            $actor->setFirstName($names[0]);
            $actor->setLastName($names[1]);
            $actor->setDob($faker->dateTimeThisCentury());

            if ($faker->boolean(20)) {
                $actor->setDod($faker->dateTimeBetween($actor->getDob(), 'now'));
            }

            $actor->setBio($faker->paragraph(6));
            $actor->setPhoto($imageUrl);

            $actorsArray[] = $actor;
            $manager->persist($actor);
        }

        return $actorsArray;
    }

    private function loadDirectors(ObjectManager $manager): array
    {
        $faker = Factory::create();
        $faker->addProvider(new Person($faker));
        $directors = $faker->directors($gender = null, $count = 50, $duplicates = false);
        $directorsArray = [];

        foreach ($directors as $item) {
            $director = new Director();
            $names = explode(' ', $item);

            $director->setFirstname($names[0]);
            $director->setLastname($names[1]);
            $director->setDob($faker->dateTimeThisCentury());

            if ($faker->boolean(20)) {
                $director->setDod($faker->dateTimeBetween($director->getDob(), 'now'));
            }

            $directorsArray[] = $director;
            $manager->persist($director);
        }

        return $directorsArray;
    }

    private function loadMovies(
        ObjectManager $manager,
        array $actorsArray,
        array $directorsArray,
        array &$categoriesArray,
        string $imageUrl
    ): void {
        $faker = Factory::create();
        $faker->addProvider(new Movie($faker));
        $movies = $faker->movies($count = 199);

        foreach ($movies as $item) {
            $movie = new Movie();

            $movie->setName($item);
            $movie->setDescription($faker->paragraph(6));
            $movie->setImage($imageUrl);
            $movie->setDuration($faker->numberBetween(60 * 60, 270 * 60));
            $movie->setReleaseDate($faker->dateTime());

            $category = $this->getOrCreateCategory($manager, $faker->movieGenre, $categoriesArray);

            shuffle($actorsArray);
            foreach (array_splice($actorsArray, 0, rand(2, 6)) as $actor) {
                $movie->addActor($actor);
            }

            $movie->setDirector($directorsArray[array_rand($directorsArray)]);
            $movie->setNbEntries(rand(0, 1000000));
            $movie->setUrl($faker->url());
            $movie->setBudget(rand(0, 50000000));
            $movie->addCategory($category);

            $manager->persist($movie);
        }
    }

    private function getOrCreateCategory(
        ObjectManager $manager,
        string $categoryName,
        array &$categoriesArray
    ): Category {
        if (!array_key_exists($categoryName, $categoriesArray)) {
            $category = new Category();
            $category->setName($categoryName);
            $manager->persist($category);

            $categoriesArray[$categoryName] = $category;
        }

        return $categoriesArray[$categoryName];
    }
}
