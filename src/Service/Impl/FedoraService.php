<?php

namespace Islandora\Service\Impl;

use Islandora\Service\IFedoraService;
use GuzzleHttp\Client;

class FedoraService implements IFedoraService {

    protected $client;
    protected $twig;

    public function __construct(Client $client, \Twig_Environment $twig) {
        $this->client = $client;
        $this->twig = $twig;
    }

    /**
     * Gets the Fedora base uri (e.g. http://localhost:8080/fcrepo/rest)
     *
     * @return string
     */
    public function getBaseUri() {
        return $this->client->getConfig('base_uri');
    }

    /**
     * Issues a GET request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     */
    public function get($url, array $options) {
        return $this->client->get($url, $options);
    }

    /**
     * Issues a POST request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function post($url, array $options) {
        return $this->client->post($url, $options);
    }

    /**
     * Issues a PUT request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function put($url, array $options) {
        return $this->client->put($url, $options);
    }

    /**
     * Issues a PATCH request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function patch($url, array $options) {
        return $this->client->patch($url, $options);
    }

    /**
     * Issues a DELETE request to Fedora.
     *
     * @param string    $url        Url
     * @param array     $options    Headers, query parameters, and body
     */
    public function delete($url, array $options) {
        return $this->client->delete($url, $options);
    }

    /**
     * Issues a POST request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePost($url, array $options, $template, array $vars) {
        $options['body'] = $this->twig->render($template, $vars);
        return $this->post($url, $options);
    }

    /**
     * Issues a PUT request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePut($url, array $options, $template, array $vars) {
        $options['body'] = $this->twig->render($template, $vars);
        return $this->put($url, $options);
    }

    /**
     * Issues a PATCH request to Fedora using template results as body.
     *
     * @param string    $url        Url
     * @param array     $options    Headers and query parameters
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     */
    public function templatePatch($url, array $options, $template, array $vars) {
        $options['body'] = $this->twig->render($template, $vars);
        return $this->patch($url, $options);
    }

    /**
     * Retrieves an RDF Graph for the resource in Fedora.
     *
     * @param string    $uri    Fedora uri
     *
     * @return EasyRdf_Graph    The graph object for the resource in Fedora
     */
    public function fetchGraph($uri) {
        return \EasyRdf_Graph::newAndLoad($uri);
    }

    public function constructUri($resource_uri, $transaction_id = "") {
        $base_uri = rtrim($this->getBaseUri(), '/');

        if (empty($resource_uri)) {
            return "$base_uri/$transaction_id";
        }

        $resource_uri = rtrim($resource_uri, '/');

        if (strcmp($resource_uri, $base_uri) == 0) {
            return "$base_uri/$transaction_id";
        }

        if (empty($transaction_id)) {
            return $resource_uri;
        }

        $exploded = explode($base_uri, $resource_uri);
        $relative_path = ltrim($exploded[1], '/');
        $exploded = explode('/', $relative_path);

        if (in_array($transaction_id, $exploded)) {
            return $resource_uri;
        }

        return implode([$base_uri, $transaction_id, $relative_path], '/');
    }
}
