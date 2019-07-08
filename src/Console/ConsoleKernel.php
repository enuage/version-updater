<?php /** @noinspection PhpUnusedParameterInspection */
/**
 * ConsoleKernel
 *
 * Created at 2019-07-08 11:22 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Console;

use Exception;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;
use Symfony\Component\Routing\RouteCollectionBuilder;

/**
 * Class ConsoleKernel
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class ConsoleKernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * {@inheritDoc}
     */
    public function registerBundles()
    {
        return [
            new FrameworkBundle(),
        ];
    }

    /**
     * {@inheritDoc}
     *
     * @throws Exception
     */
    protected function configureContainer(ContainerBuilder $containerBuilder, LoaderInterface $loader)
    {
        $containerBuilder->loadFromExtension(
            'framework',
            [
                'secret' => 'e2b5906a-0860-4a2d-816c-04fd8c13e03e',
            ]
        );
    }

    /**
     * {@inheritDoc}
     */
    protected function configureRoutes(RouteCollectionBuilder $routes)
    {
    }
}
