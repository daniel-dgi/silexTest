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

use Islandora\Service\ICollectionService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for collections.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class CollectionController {

    protected $service;   // Collection service

    /**
     * Ctor.
     *
     * @param ICollectionService $service   Collection service.
     */
    public function __construct(ICollectionService $service) {
        $this->service = $service;
    }

    /**
     * Fetches RDF from Fedora based on Drupal id.
     *
     * @return array   List of collection ids.
     */
    public function index() {
        return $this->service->index();
    }

    /**
     * Creates a collection in Fedora based on Drupal node data.
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
}
