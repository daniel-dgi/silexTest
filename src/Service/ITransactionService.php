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
interface ITransactionService
{
    /**
     * Gets the status of a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string   Fedora response
     */
    public function status($id);

    /**
     * Creates a new transaction.
     *
     * @return string    Transaction id
     */
    public function create();

    /**
     * Extends a transaction.
     *
     * @param string    $id         Transaction id
     *
     * @return string    Fedora response
     */
    public function extend($id);

    /**
     * Commits a transaction.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function commit($id);

    /**
     * Creates a resource in Fedora from a template.
     *
     * @param string $id       Transaction id
     *
     * @return boolean
     */
    public function rollback($id);

}


