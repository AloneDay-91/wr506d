<?php

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\NullOutput;
use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\EntityManagerInterface;
use App\Kernel;

require dirname(__DIR__).'/vendor/autoload.php';

if (method_exists(Dotenv::class, 'bootEnv')) {
    (new Dotenv())->bootEnv(dirname(__DIR__).'/.env');
}

if ($_SERVER['APP_DEBUG']) {
    umask(0000);
}

// Ensure the test kernel is booted for database operations
$kernel = new Kernel('test', true);
$kernel->boot();

/** @var EntityManagerInterface $entityManager */
$entityManager = $kernel->getContainer()->get('doctrine.orm.entity_manager');

// Create a SchemaTool instance
$schemaTool = new SchemaTool($entityManager);

// Get metadata for all managed entities
$metadatas = $entityManager->getMetadataFactory()->getAllMetadata();

// Drop and create schema
$schemaTool->dropSchema($metadatas);
$schemaTool->createSchema($metadatas);

// Optionally, load fixtures if any (assuming you have them)
// $application = new Application($kernel);
// $application->setAutoExit(false);
// $input = new ArrayInput([
//    'command' => 'doctrine:fixtures:load',
//    '--no-interaction' => true,
// ]);
// $application->run($input, new NullOutput());
