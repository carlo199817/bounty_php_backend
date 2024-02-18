<?php
// bootstrap.php
use Doctrine\DBAL\DriverManager;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\ORMSetup;

require_once "vendor/autoload.php";

// Create a simple "default" Doctrine ORM configuration for Attributes
$config = ORMSetup::createAttributeMetadataConfiguration(
    paths: array(__DIR__."/src"),
    isDevMode: true,
); 
// or if you prefer XML
// $config = ORMSetup::createXMLMetadataConfiguration(
//    paths: array(__DIR__."/config/xml"),
//    isDevMode: true,
//);

// configuring the database connection
$connectionParams = [
    'dbname' => 'PHP',
    'user' => 'test',
    'password' => 'Secret_1234', 
    'host' => '127.0.0.1',
    'port' => '8889',
    'driver' => 'pdo_mysql',
];
$connection = DriverManager::getConnection($connectionParams);


// obtaining the entity manager
$entityManager = new EntityManager($connection, $config);
?>