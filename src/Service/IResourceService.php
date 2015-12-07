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
 * Interface for resource service.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
interface IResourceService
{
    /**
     * Fetches RDF from Fedora based on Drupal id.
     *
     * @param string    $id         Drupal id
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   RDF from Fedora
     */
    public function find($id, $mimetype);

    /**
     * Fetches and parses RDF from Fedora based on Drupal id.
     *
     * @param string            $id Drupal id
     *
     * @return EasyRdf_Graph    RDF from Fedora
     */
    public function findGraph($id);

    /**
     * Creates a resource in Fedora.
     *
     * @param string $rdf       RDF
     * @param string $mimetype  Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function create($rdf, $mimetype);

    /**
     * Creates a resource in Fedora from a template.
     *
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function templateCreate($template, array $data, $mimetype);

    /**
     * Creates a resource in Fedora based on Drupal node data.
     *
     * @param array $node   Drupal node
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function nodeCreate(array $node);

    /**
     * Updates a resource in Fedora.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $rdf        RDF
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Fedora response
     */
    public function upsert($id, $rdf, $mimetype);

    /**
     * Updates a resource in Fedora from a template.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     * @param string    $mimetype   Mimetype of RDF
     *
     * @return string   Fedora response
     */
    public function templateUpsert($id, $template, array $data, $mimetype);

    /**
     * Updates a resource in Fedora using SPARQL Update.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $sparql     Sparql query
     *
     * @return string   Fedora response
     */
    public function sparqlUpdate($id, $rdf);

    /**
     * Updates a resource in Fedora from a template.
     *
     * @param string    $id         Drupal id of the resource
     * @param string    $template   Path to template file
     * @param array     $data       Template data
     *
     * @return string   Fedora response
     */
    public function templateSparqlUpdate($id, $template, array $data);

    /**
     * Updates a resource in Fedora based on Drupal node data.
     *
     * @param string    $id     Drupal id of the resource
     * @param array     $node   Drupal node
     *
     * @return string   Fedora response
     */
    public function nodeSparqlUpdate($id, array $node);

    /**
     * Deletes a resource in Fedora based on Drupal id.
     *
     * @param string    $id     Drupal id of the resource
     *
     * @return string   Uri of newly created resource in Fedora
     */
    public function delete($id);
}

