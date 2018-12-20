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

namespace Enuage\VersionUpdaterBundle\Tests;

use Enuage\VersionUpdaterBundle\VersionUpdaterBundle;
use Symfony\Bundle\FrameworkBundle\FrameworkBundle;
use Symfony\Component\Config\Loader\LoaderInterface;
use Symfony\Component\HttpKernel\Bundle\BundleInterface;
use Symfony\Component\HttpKernel\Kernel;

/**
 * Class AppKernel
 *
 * @package Enuage\VersionUpdaterBundle\Tests
 */
class AppKernel extends Kernel
{
    /**
     * Returns an array of bundles to register.
     *
     * @return BundleInterface[] An array of bundle instances
     */
    public function registerBundles(): array
    {
        $bundles = [
            new FrameworkBundle(),
            new VersionUpdaterBundle(),
        ];

        return $bundles;
    }

    /**
     * Loads the container configuration.
     *
     * @param LoaderInterface $loader
     *
     * @throws \Exception
     */
    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config.yml');
    }
}
