<?php
/**
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code. Or visit
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace Enuage\VersionUpdaterBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration
 *
 * @package Enuage\VersionUpdaterBundle\DependencyInjection
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('enuage_version_updater');
        $rootNode->append($this->getFilesNode());
        $rootNode->append($this->getJsonNode());

        return $treeBuilder;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function getFilesNode(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('files');
        $node->arrayPrototype()->scalarPrototype()->end();

        return $node;
    }

    /**
     * @return ArrayNodeDefinition
     */
    private function getJsonNode(): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition('json');
        $node->arrayPrototype()->scalarPrototype()->end();

        return $node;
    }
}
