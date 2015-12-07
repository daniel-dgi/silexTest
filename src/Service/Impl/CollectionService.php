<?php

/**
 * This file is part of Islandora.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * PHP Version 5.5.9
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */

namespace Islandora\Service\Impl;

use Rhumsaa\Uuid\Uuid;
use Symfony\Component\HttpFoundation\Request;
use Islandora\Service\ICollectionService;
use Islandora\Service\IResourceService;
use Islandora\Service\ITriplestoreService;
use Islandora\Service\IFedoraService;

/**
 * Symfony service for Collections.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class CollectionService implements ICollectionService {
    protected $resource;      // Resource service
    protected $triplestore;   // Triplestore service
    protected $fedora;   // Triplestore service

    /**
     * Ctor.
     *
     * @param IResourceService        $resource         Resource service.
     * @param ITriplestoreService   $triplestore    Triplestore service.
     * @param IFedoraService   $fedora    Fedora service.
     */
    public function __construct(IResourceService $resource,
                                ITriplestoreService $triplestore,
                                IFedoraService $fedora) {
        $this->resource = $resource;
        $this->triplestore = $triplestore;
        $this->fedora = $fedora;
    }

    /**
     * Gets a list of all collections
     *
     * @return array   List of collectons
     */
    public function index() {
        return [];
    }

    /**
     * Creates a collection in Fedora.
     *
     * @param string $rdf       RDF
     * @param string $mimetype  Mimetype of rdf format
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function create($rdf, $mimetype, $transaction_id = "") {
        // Load default rdf if none is provided.
        if (empty($rdf)) {
            $mimetype = 'text/turtle';
            $collection_uri = $this->resource->templateCreate(
                'pcdm-collection.ttl',
                ['id' => Uuid::uuid4()->toString()],
                $mimetype,
                $transaction_id
            );
        } else {
            $collection_uri = $this->resource->create(
                $rdf,
                $mimetype,
                $transaction_id
            );
        }

        // Add its 'members' indirect-container to maintain the pcdm:hasMember
        // relationship.
        $options = [
            'headers' => [
                'Accept' => 'text/turtle',
                'Content-Type' => 'text/turtle',
            ],
        ];
        $members_uri = $this->fedora->templatePut(
            $this->fedora->constructUri("$collection_uri/members", $transaction_id),
            $options,
            'ldp-indirect.ttl.twig',
            ['uri' => $collection_uri]
        );

        // Return the collection uri.
        return $collection_uri;
    }

    /**
     * Creates a collection in Fedora from a template.
     *
     * @param string    $template   RDF template
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of rdf format
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function templateCreate($template, array $data, $mimetype, $transaction_id = "") {
        $collection_uri = $this->resource>templateCreate(
            $template,
            $data,
            $mimetype,
            $transaction_id
        );

        // Add its 'members' indirect-container to maintain the pcdm:hasMember
        // relationship.
        $options = [
            'headers' => [
                'Accept' => 'text/turtle',
                'Content-Type' => 'text/turtle',
            ],
        ];
        $members_uri = $this->fedora->templatePut(
            $this->fedora->constructUri("$collection_uri/members", $transaction_id),
            $options,
            'ldp-indirect.ttl.twig',
            ['uri' => $collection_uri]
        );

        // Return the collection uri.
        return $collection_uri;
    }

    /**
     * Adds a resource to a collection in Fedora.
     *
     * @param string    $collection_uri Fedora uri of the collection
     * @param string    $child_uri      Fedora uri of the child resource
     *
     * @return string   Uri of the resource in Fedora
     */
    //public function addToCollection($child_uri, $collection_uri) {
        //// Produce a turtle file for the proxy using a twig template.
        //$turtle = $this->twig->render('proxy.ttl.twig', ['uri' => $child_uri, 'parent_uri' => $collection_uri]);

        //// POST the proxy to fedora under the collection's members container.
        //$fedora_response = $this->fedora->post("$collection_uri/members", [
            //'headers' => [
                //'Accept' => 'text/turtle',
                //'Content-Type' => 'text/turtle',
            //],
            //'body' => $turtle,
        //]);

        //return $fedora_response;
    //}

    /**
     * Removes a resource from a collection in Fedora.
     *
     * @param string    $child_uri      Fedora uri of the child resource
     * @param string    $collection_uri Fedora uri of the collection
     *
     * @return string   Uri of the resource in Fedora
     */
    //public function removeFromCollection($child_uri, $collection_uri) {
        //$sparql = $this->twig->render('getProxyUri.sparql', [
            //'id' => $child_uri,
            //'collection_id' => $collection_uri,
        //]);
        //$sparql_response = $this->triplestore->post("", [
            //'query' => [
                //'format' => 'json',
                //'query' => $sparql,
            //],
        //]);

        //// JSON decode response
        //$sparql_response = $sparql_response->getBody();
        //$sparql_response = json_decode($sparql_response, true);

        //// Throw an exception if there's no results.
        //if (!isset($sparql_response['results']['bindings'][0]['s']['value'])) {
            //throw new \Exception("No proxy for $child_uri in $collection_uri", 404);
        //}

        //// Return the uri.
        //$proxy_uri = $sparql_response['results']['bindings'][0]['s']['value'];

        //return $this->fedora->delete($proxy_uri);
    //}

    /**
     * Moves a resource to a different collection.
     *
     * @param string    $child_uri          Fedora uri of the child resource
     * @param string    $source_uri         Fedora uri of the collection
     * @param string    $destination_uri    Fedora uri of the collection
     *
     * @return string   Uri of the resource in Fedora
     */
    //public function migrateToCollection($child_uri, $source_uri, $destination_uri) {
        //$this->removeFromCollection($child_uri, $source_uri);
        //$this->addToCollection($child_uri, $destination_uri);
    //}
}
