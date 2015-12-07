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

use Islandora\Service\ITriplestoreService;
use GuzzleHttp\Client;

class TriplestoreService implements ITriplestoreService {
    protected $client;
    protected $twig;

    public function __construct(Client $client, \Twig_Environment $twig) {
        $this->client = $client;
        $this->twig = $twig;
    }

    public function query($sparql) {
        $response = $this->client->post("", [
            'query' => [
                'format' => 'json',
                'query' => $sparql,
            ],
        ]);

        return new \EasyRdf_Sparql_Result($response->getBody(), 'application/sparql-results+json');
    }

    public function templateQuery($template, array $data) {
        $sparql = $this->twig->render($template, $data);
        return $this->query($sparql);
    }
}
