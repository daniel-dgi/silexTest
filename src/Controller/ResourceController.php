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

namespace Islandora\Controller;

use Islandora\Service\IResourceService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for resources.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class ResourceController {

    protected $service;   // Resource service

    /**
     * Ctor.
     *
     * @param IResourceService $service   Resource service.
     */
    public function __construct(IResourceService $service) {
        $this->service = $service;
    }

    /**
     * Fetches RDF from Fedora based on Drupal id.
     *
     * @param string    $id Drupal id
     *
     * @return string   RDF from Fedora
     */
    public function find($id, Request $request) {
        $mimetype = $request->headers->get("Accept");
        return $this->service->find($id, $mimetype);
    }

    /**
     * Creates a resource in Fedora.
     *
     * @param Request   $request    Request
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function create(Request $request) {
        $mimetype = $request->headers->get("Content-Type");
        $rdf = $request->getContent();
        $transaction_id = $request->query->get("transaction_id");
        return $this->service->create($rdf, $mimetype, $transaction_id);
    }

    /**
     * Updates a resource in Fedora with RDF.
     *
     * @param string    $id     Drupal id of the resource
     * @param Request   $request    Request
     *
     * @return string   Fedora response
     */
    public function upsert($id, Request $request) {
        $mimetype = $request->headers->get("Content-Type");
        $rdf = $request->getContent();
        $transaction_id = $request->query->get("transaction_id");
        return $this->service->upsert($id, $rdf, $mimetype, $transaction_id);
    }

    /**
     * Updates a resource in Fedora using SPARQL Update.
     *
     * @param string    $id     Drupal id of the resource
     * @param Request   $request    Request
     *
     * @return string   Fedora response
     */
    public function sparqlUpdate($id, Request $request) {
        $sparql = $request->getContent();
        $transaction_id = $request->query->get("transaction_id");
        return $this->service->sparqlUpdate($id, $sparql, $transaction_id);
    }

    /**
     * Deletes a resource in Fedora based on Drupal id.
     *
     * @param string    $id     Drupal id of the resource
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function delete($id, Request $request) {
        $transaction_id = $request->query->get("transaction_id");
        return $this->service->delete($id, $transaction_id);
    }
}

