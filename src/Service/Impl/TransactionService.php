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

use Islandora\Service\ITransactionService;
use Islandora\Service\IFedoraService;
use Islandora\Service\Impl\FedoraService;

/**
 * Transaction service.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class TransactionService implements ITransactionService
{
    protected $fedora;

    public function __construct(IFedoraService $fedora) {
        $this->fedora = $fedora;
    }

    /**
     * Gets the status of a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string   Fedora response
     */
    public function status($id) {
        return $this->fedora->get($id)->getBody();
    }

    /**
     * Creates a new transaction.
     *
     * @return string    Transaction id
     */
    public function create() {
        // For some reason, guzzle doesn't like combining the base uri with
        // a route that has a colon in it :(
        // Doing it manually.
        $base_uri = $this->fedora->getBaseUri();
        $fedora_response = $this->fedora->post("$base_uri/fcr:tx", []);
        $transaction_uri = $fedora_response->getHeader('Location')[0];
        $exploded = explode("$base_uri/", $transaction_uri);
        return $exploded[1];
    }

    /**
     * Extends a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string    Fedora response
     */
    public function extend($id) {
        // For some reason, guzzle doesn't like combining the base uri with
        // a route that has a colon in it :(
        // Doing it manually.
        $base_uri = $this->fedora->getBaseUri();
        $fedora_response = $this->fedora->post("$base_uri/$id", []);
        return $id;
        //$transaction_uri = $fedora_response->getHeader('Location')[0];
        //$exploded = explode("$base_uri/", $transaction_uri);
        //return $exploded[1];
    }

    /**
     * Commits a transaction.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function commit($id) {
        // For some reason, guzzle doesn't like combining the base uri with
        // a route that has a colon in it :(
        // Doing it manually.
        $base_uri = $this->fedora->getBaseUri();
        return $this->fedora->post("$base_uri/$id/fcr:tx/fcr:commit", [])->getBody();
    }

    /**
     * Rollback a transaction.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function rollback($id) {
        // For some reason, guzzle doesn't like combining the base uri with
        // a route that has a colon in it :(
        // Doing it manually.
        $base_uri = $this->fedora->getBaseUri();
        return $this->fedora->post("$base_uri/$id/fcr:tx/fcr:rollback", [])->getBody();
    }
}
