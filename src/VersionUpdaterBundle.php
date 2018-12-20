<?php
/**
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updated command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code. Or visit
 * https://www.gnu.org/licenses/gpl-3.0.en.html
 */

namespace Enuage\VersionUpdaterBundle;

use Enuage\VersionUpdaterBundle\DependencyInjection\EnuageVersionUpdaterExtension;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class VersionUpdaterBundle
 *
 * @package Enuage\VersionUpdaterBundle
 */
class VersionUpdaterBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function getContainerExtension(): Extension
    {
        return new EnuageVersionUpdaterExtension();
    }

}
