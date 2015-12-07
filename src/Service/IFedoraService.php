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
 * Interface for Fedora interaction.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
interface IFedoraService
{
    /**
     * Gets the Fedora base uri (e.g. http://localhost:8080/fcrepo/rest)
     *
     * @return string
     */
    public function getBaseUri();

    /**
     * Issues a GET request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     */
    public function get($url, array $options);

    /**
     * Issues a POST request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function post($url, array $options);

    /**
     * Issues a PUT request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function put($url, array $options);

    /**
     * Issues a PATCH request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function patch($url, array $options);

    /**
     * Issues a DELETE request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function delete($url, array $options);

    /**
     * Issues a POST request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePost($url, array $options, $template, array $vars);

    /**
     * Issues a PUT request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePut($url, array $options, $template, array $vars);

    /**
     * Issues a PATCH request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePatch($url, array $options, $template, array $vars);

    /**
     * Retrieves an RDF Graph for the resource in Fedora.
     *
     * @param string    $uri    Fedora uri
     *
     * @return EasyRdf_Graph    The graph object for the resource in Fedora
     */
    public function fetchGraph($uri);

    public function constructUri($resource_uri, $transaction_id = "");
}

