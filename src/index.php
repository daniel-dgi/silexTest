<?php

namespace Islandora;

require_once __DIR__.'/../vendor/autoload.php';

use GuzzleHttp\Client;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Silex\Application;
use Islandora\Service\Impl\CollectionService;
use Islandora\Service\Impl\ResourceService;
use Islandora\Service\Impl\TransactionService;
use Islandora\Controller\CollectionController;
use Islandora\Controller\ResourceController;
use Islandora\Controller\TransactionController;
use Islandora\Service\Impl\Sparqlizer;
use Islandora\Service\Impl\FedoraService;
use Islandora\Service\Impl\TriplestoreService;
use Silex\Provider\TwigServiceProvider;
use Silex\Provider\ServiceControllerServiceProvider;

date_default_timezone_set('UTC');

$app = new Application();
$app['debug'] = true;
$app->register(new ServiceControllerServiceProvider());
$app->register(new TwigServiceProvider(), [
    'twig.path' => __DIR__ . '/../templates',
]);
$app['triplestore'] = function () use($app) {
    $client = new Client(['base_uri' => 'localhost:8080/bigdata/namespace/kb/sparql']);
    return new TriplestoreService($client, $app['twig']);
};
$app['sparqlizer'] = function() use($app) {
    return new Sparqlizer($app['twig']);
};
$app['fedora'] = function () use($app){
    $client = new Client(['base_uri' => 'localhost:8080/fcrepo/rest']);
    return new FedoraService($client, $app['twig']);
};
$app['resource.service'] = function() use($app) {
    return new ResourceService($app['fedora'], $app['triplestore'], $app['twig'], $app['sparqlizer']);
};
$app['resource.controller'] = function() use($app) {
    return new ResourceController($app['resource.service']);
};
$app['collection.service'] = function() use($app) {
    return new CollectionService($app['resource.service'], $app['triplestore'], $app['fedora']);
};
$app['collection.controller'] = function() use($app) {
    return new CollectionController($app['collection.service']);
};
$app['transaction.service'] = function() use($app) {
    return new TransactionService($app['fedora']);
};
$app['transaction.controller'] = function() use($app) {
    return new TransactionController($app['transaction.service']);
};

$app->get('islandora/resource/{id}', 'resource.controller:find');
$app->post('islandora/resource/', 'resource.controller:create');
$app->put('islandora/resource/{id}', 'resource.controller:upsert');
$app->patch('islandora/resource/{id}', 'resource.controller:sparqlUpdate');
$app->delete('islandora/resource/{id}', 'resource.controller:delete');

$app->get('islandora/transaction/{id}', 'transaction.controller:status');
$app->post('islandora/transaction/', 'transaction.controller:create');
$app->post('islandora/transaction/{id}', 'transaction.controller:extend');
$app->post('islandora/transaction/{id}/commit', 'transaction.controller:commit');
$app->post('islandora/transaction/{id}/rollback', 'transaction.controller:rollback');

//$app->get('islandora/members/{id}', 'members.controller:find');
//$app->post('islandora/members/{id}/{child_id}', 'members.controller:add');
//$app->delete('islandora/members/{id}/{child_id}', 'members.controller:remove');
//$app->patch('islandora/members/{id}/{child_id}/{destination_id}', 'members.controller:migrate');

$app->get('islandora/collection/', 'collection.controller:index');
$app->post('islandora/collection/', 'collection.controller:create');

//$app->get('islandora/files/{id}', 'files.controller:find');
//$app->post('islandora/files/{id}/{child_id}', 'files.controller:add');
//$app->delete('islandora/files/{id}/{child_id}', 'files.controller:remove');
//$app->patch('islandora/files/{id}/{child_id}/{destination_id}', 'files.controller:migrate');

//$app->get('islandora/object/{id}', 'object.controller:find');
//$app->post('islandora/object/', 'object.controller:create');
//$app->put('islandora/object/{id}', 'object.controller:update');
//$app->patch('islandora/object/{id}', 'object.controller:sparqlUpdate');
//$app->delete('islandora/object/{id}', 'object.controller:delete');

$app->run();
