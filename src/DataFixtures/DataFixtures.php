<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker;
use App\Entity\Actor;
use App\Entity\Movie;
use App\Entity\Category;

class DataFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = \Faker\Factory::create();
        $faker->addProvider(new \Xylis\FakerCinema\Provider\Person($faker));

        $actors = $faker->actors($gender = null, $count = 190, $duplicates = false);

        $categoriesArray = [];
        $actorsArray = [];

        $imageUrl = 'https://placehold.co/600x400';

        foreach ($actors as $item) {
            $actor = new Actor();

            $fullname = $item;
            $names = explode(' ', $fullname);

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

        $fakerMovie = \Faker\Factory::create();
        $fakerMovie->addProvider(new \Xylis\FakerCinema\Provider\Movie($fakerMovie));
        $movies = $fakerMovie->movies($count = 199);

        foreach ($movies as $item) {
            $movie = new Movie();

            $movie->setName($item);
            $movie->setDescription($fakerMovie->paragraph(6));
            $movie->setImage($imageUrl);

            $durationMin = 60 * 60;
            $durationMax = 270 * 60;

            $movie->setDuration($fakerMovie->numberBetween($durationMin, $durationMax));
            $movie->setReleaseDate($fakerMovie->dateTime());

            $categoryName = $fakerMovie->movieGenre;

            if (!array_key_exists($categoryName, $categoriesArray)) {

                $category = new Category();
                $category->setName($categoryName);
                $manager->persist($category);

                $categoriesArray[$categoryName] = $category;
            } else {
                $category = $categoriesArray[$categoryName];
            }
            shuffle($actorsArray);
            foreach (array_splice($actorsArray, 0, rand(2,6))as $actor){
                $movie->addActor($actor);
            }
            $movie->addCategory($category);
            $manager->persist($movie);
        }
        $manager->flush();
    }
}
