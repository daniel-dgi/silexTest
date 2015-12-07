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
 * Interface for triplestore interaction.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
interface ITriplestoreService
{
    /**
     * Executes a sparql query.
     *
     * @param string    $sparql Sparql query
     *
     * @return array    JSON decoded results
     */
    public function query($sparql);

    /**
     * Constructs a sparql query from a template and executes it.
     *
     * @param string    $template   Path to template file
     * @param array     $vars       Template variables
     *
     * @return array    JSON decoded results
     */
    public function templateQuery($template, array $args);
}
