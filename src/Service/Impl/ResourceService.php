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
use Islandora\Service\IResourceService;
use Islandora\Service\IFedoraService;
use Islandora\Service\ITriplestoreService;
use Islandora\Service\ISparqlizer;

/**
 * Symfony service for Resources.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class ResourceService implements IResourceService {
    protected $fedora;        // Fedora service
    protected $triplestore;   // Triplestore service
    protected $twig;          // Twig service
    protected $sparqlizer;    // Sparqlizer service

    /**
     * Ctor.
     *
     * @param IFedoraService        $fedora         Fedora service.
     * @param ITriplestoreService   $triplestore    Triplestore service.
     * @param Twig_Environment      $twig           Twig service.
     * @param ISparqlizer           $sparqlizer     Sparqlizer service.
     */
    public function __construct(IFedoraService $fedora,
                                ITriplestoreService $triplestore,
                                \Twig_Environment $twig,
                                ISparqlizer $sparqlizer) {
        $this->fedora = $fedora;
        $this->triplestore = $triplestore;
        $this->twig = $twig;
        $this->sparqlizer = $sparqlizer;
    }

    /**
     * Fetches RDF from Fedora based on Drupal id.
     *
     * @param string    $id         Drupal id
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   RDF from Fedora
     */
    public function find($id, $mimetype) {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        if (empty($resource_uri)) {
            throw new \Exception("No resource exists associated with id: $id.", 404);
        }

        // Retrieve the RDF from fedora.
        $fedora_response = $this->fedora->get($resource_uri, [
            'headers' => [
                'Accept' => $mimetype,
            ]
        ]);
        return $fedora_response->getBody();
    }

    /**
     * Fetches and parses RDF from Fedora based on Drupal id.
     *
     * @param string            $id Drupal id
     *
     * @return EasyRdf_Graph    RDF from Fedora
     */
    public function findGraph($id) {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        if (empty($resource_uri)) {
            throw new \Exception("No resource exists associated with id: $id.", 404);
        }

        // Retrieve the RDF from fedora.
        $fedora_response = $this->fedora->get($resource_uri, [
            'headers' => [
                'Accept' => 'text/turtle',
            ]
        ]);

        return \EasyRdf_Graph::newAndLoad($fedora_response->getBody(), 'text/turtle');
    }

    /**
     * Creates a resource in Fedora.
     *
     * @param string $rdf       RDF
     * @param string $mimetype  Mimetype of rdf format
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function create($rdf, $mimetype, $transaction_id = "") {
        $fedora_uri = $this->fedora->constructUri("", $transaction_id);

        if (empty($rdf)) {
            $mimetype = 'text/turtle';
            $fedora_response = $this->fedora->templatePost(
                $fedora_uri,
                [
                    'headers' => [
                        'Accept' => $mimetype,
                        'Content-Type' => $mimetype,
                    ],
                ],
                'default-resource.ttl',
                ['id' => Uuid::uuid4()->toString()]
            );
        } else {
            $fedora_response = $this->fedora->post(
                $base_uri,
                [
                    'headers' => [
                        'Accept' => $mimetype,
                        'Content-Type' => $mimetype,
                    ],
                    'body' => $rdf,
                ]
            );
        }

        return $fedora_response->getBody();
    }

    /**
     * Creates a resource in Fedora from a template.
     *
     * @param string    $template   RDF template
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of rdf format
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function templateCreate($template, array $data, $mimetype, $transaction_id = "") {
        $fedora_response = $this->fedora->templatePost(
            $this->fedora->constructUri("", $transaction_id),
            [
                'headers' => [
                    'Accept' => $mimetype,
                    'Content-Type' => $mimetype,
                ],
            ],
            $template,
            $data
        );

        return $fedora_response->getBody();
    }

    /**
     * Creates a resource in Fedora based on Drupal node data.
     *
     * @param array $node   Drupal node
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function nodeCreate(array $node, $transaction_id = "") {
        $sparql = $this->sparqlizer->nodeToSparql($node);
        return $this->create($sparql, 'application/sparql-update', $transaction_id);
    }

    /**
     * Updates a resource in Fedora.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $rdf        RDF
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function upsert($id, $rdf, $mimetype, $transaction_id = "") {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        $options = [
            'headers' => [
                'Accept' => $mimetype,
                'Content-Type' => $mimetype,
            ],
            'body' => $rdf,
        ];

        $fedora_uri = $this->fedora->constructUri($resource_uri, $transaction_id);

        if (empty($resource_uri)) {
            $fedora_response = $this->fedora->post(
                $fedora_uri,
                $options
            );
        } else {
            $fedora_response = $this->fedora->put(
                $fedora_uri,
                $options
            );
        }

        return $fedora_response->getBody();
    }

    /**
     * Updates a resource in Fedora from a template.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function templateUpsert($id, $template, array $data, $mimetype, $transaction_id = "") {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        $options = [
            'headers' => [
                'Accept' => $mimetype,
                'Content-Type' => $mimetype,
            ],
        ];

        $fedora_uri = $this->fedora->constructUri($resource_uri, $transaction_id);

        if (empty($resource_uri)) {
            $fedora_response = $this->fedora->templatePost(
                $fedora_uri,
                $options,
                $template,
                $data
            );
        } else {
            $fedora_response = $this->fedora->templatePut(
                $fedora_uri,
                $options,
                $template,
                $data
            );
        }

        return $fedora_response->getBody();
    }

    /**
     * Updates a resource in Fedora using SPARQL Update.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $sparql     Sparql query
     *
     * @return string   Fedora response
     */
    public function sparqlUpdate($id, $sparql, $transaction_id = "") {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        if (empty($resource_uri)) {
            throw new \Exception("No resource exists associated with id: $id.", 404);
        }

        $options = [
            'headers' => [
                'Accept' => 'application/sparql-update',
                'Content-Type' => 'application/sparql-update',
            ],
            'body' => $sparql,
        ];

        $fedora_response = $this->fedora->patch(
            $this->fedora->constructUri($resource_uri, $transaction_id),
            $options
        );

        return $fedora_response->getBody();
    }

    /**
     * Updates a resource in Fedora from a template.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     *
     * @return string   Fedora response
     */
    public function templateSparqlUpdate($id, $template, array $data, $transaction_id = "") {
        // Get the uri in fedora based on drupal id.
        $resource_uri = $this->getResourceUri($id);

        if (empty($resource_uri)) {
            throw new \Exception("No resource exists associated with id: $id.", 404);
        }

        $options = [
            'headers' => [
                'Accept' => 'appliation/sparql-update' ,
                'Content-Type' => 'appliation/sparql-update',
            ],
        ];

        $fedora_response = $this->fedora->templatePatch(
            $this->fedora->constructUri($resource_uri, $transaction_id),
            $options,
            $template,
            $data
        );

        return $fedora_response->getBody();
    }

    /**
     * Updates a resource in Fedora based on Drupal node data.
     *
     * @param string    $id     Drupal id of the resource
     * @param array     $node   Drupal node
     *
     * @return string   Fedora response
     */
    public function nodeSparqlUpdate($id, array $node, $transaction_id = "") {
        $sparql = $this->sparqlizer->nodeToSparql($node);
        return $this->sparqlUpdate($id, $sparql, $transaction_id);
    }

    /**
     * Deletes a resource in Fedora based on Drupal id.
     *
     * @param string    $id     Drupal id of the resource
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function delete($id, $transaction_id = "") {
        $resource_uri = $this->getResourceUri($id);

        if (empty($resource_uri)) {
            throw new \Exception("No resource exists associated with id: $id.", 404);
        }

        $result = $this->triplestore->templateQuery(
            'getProxiesFor.sparql.twig',
            ['id' => $resource_uri]
        );

        $base_uri = $this->fedora->getBaseUri();

        foreach ($result as $row) {
            $this->fedora->delete(
                $this->fedora->constructUri($row->s, $transaction_id),
                []
            );
        }

        // DELETE the object in Fedora.
        $fedora_response = $this->fedora->delete(
            $this->fedora->constructUri($resource_uri, $transaction_id),
            []
        );

        return $fedora_response->getBody();
    }

    /**
     * Queries the triplestore for a Fedora uri associated with a Drupal id.
     *
     * @param string    $id Drupal id of the resource
     *
     * @return string   Uri of the resource in Fedora
     */
    public function getResourceUri($id) {
        // POST the query to the triplestore.
        $result = $this->triplestore->templateQuery('get-resource-uri.sparql', ['id' => $id]);

        foreach ($result as $row) {
            return $row->s;
        }

        return null;
    }
}

