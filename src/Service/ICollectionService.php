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

namespace Islandora\Service;

/**
 * Interface for collection service.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
interface ICollectionService
{
    /**
     * Gets a list of all collections
     *
     * @return array   List of collectons
     */
    public function index();

    /**
     * Creates a collection in Fedora.
     *
     * @param string $rdf       RDF
     * @param string $mimetype  Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function create($rdf, $mimetype, $transaction_id = "");

    /**
     * Creates a collection in Fedora from a template.
     *
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function templateCreate($template, array $data, $mimetype, $transaction_id = "");
}
