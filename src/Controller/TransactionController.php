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

use Islandora\Service\ITransactionService;
use Symfony\Component\HttpFoundation\Request;

/**
 * Controller for transactions.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
class TransactionController {

    protected $service;   // Transaction service

    /**
     * Ctor.
     *
     * @param ITransactionService $service   Collection service.
     */
    public function __construct(ITransactionService $service) {
        $this->service = $service;
    }

    /**
     * Gets the status of a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string   Fedora response
     */
    public function status($id) {
        return $this->service->status($id);
    }

    /**
     * Creates a new transaction.
     *
     * @return string    Transaction id
     */
    public function create() {
        return $this->service->create();
    }

    /**
     * Extends a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string    Fedora response
     */
    public function extend($id) {
        return $this->service->extend($id);
    }

    /**
     * Commits a transaction.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function commit($id) {
        return $this->service->commit($id);
    }

    /**
     * Creates a resource in Fedora from a template.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function rollback($id) {
        return $this->service->rollback($id);
    }
}

