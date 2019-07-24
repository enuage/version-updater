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
    public const CONFIG_ROOT = 'enuage_version_updater';
    public const CONFIG_FILE = '.enuage';

    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::CONFIG_ROOT);
        $rootNode = $treeBuilder->getRootNode();
        $rootNode->append($this->getFilesNode('files'));
        $rootNode->append($this->getFilesNode('json'));
        $rootNode->append($this->getFilesNode('yaml'));

        return $treeBuilder;
    }

    /**
     * @param string $title
     *
     * @return ArrayNodeDefinition
     */
    private function getFilesNode(string $title): ArrayNodeDefinition
    {
        $node = new ArrayNodeDefinition($title);
        $node->arrayPrototype()->scalarPrototype()->end();

        return $node;
    }
}
