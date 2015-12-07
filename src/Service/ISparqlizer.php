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
 * Converts Drupal node JSON for a collection to a SPARQL Update query.
 *
 * @category Islandora
 * @package  Islandora
 * @author   Daniel Lamb <daniel@discoverygarden.ca>
 * @license  http://www.gnu.org/licenses/gpl-3.0.en.html GPL
 * @link     http://www.islandora.ca
 */
Interface ISparqlizer
{
    /**
     * Converts Drupal node JSON for a collection to a SPARQL Update query.
     *
     * @param array  $node  Drupal node as array.
     *
     * @return string   The SPARQL Update query.
     */
    public function nodeToSparql(array $node);
}
