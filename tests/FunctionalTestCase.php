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

use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\HttpKernel\KernelInterface;

class FunctionalTestCase extends KernelTestCase
{
    /**
     * @var KernelInterface $kernel
     */
    private $testKernel;

    /**
     * @return KernelInterface
     */
    protected function getKernel(): KernelInterface
    {
        return $this->testKernel;
    }

    /**
     * {@inheritdoc}
     */
    protected function setUp()
    {
        $kernel = self::bootKernel(['environment' => 'test', 'debug' => true]);
        $this->testKernel = $kernel;
    }
}
