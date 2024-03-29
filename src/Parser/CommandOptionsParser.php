<?php
/**
 * CommandSettingsParser
 *
 * Created at 2019-06-23 2:14 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Parser;

use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;
use Symfony\Component\Console\Input\InputInterface;

/**
 * Class CommandSettingsParser
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
final class CommandOptionsParser
{
    /**
     * @param InputInterface $input
     *
     * @return VersionOptions
     *
     * @throws Exception
     */
    public static function parse(InputInterface $input): VersionOptions
    {
        $options = new VersionOptions();

        $options->setVersion($input->getArgument('version'));

        $options->downgrade($input->hasParameterOption('--down'));

        $options->getMainModifiers()->downgradeAll($options->isDowngrade());
        $options->getPreReleaseModifiers()->downgradeAll($options->isDowngrade());

        if ($input->hasParameterOption('--release')) {
            $options->release();
        }

        foreach (Version::MAIN_VERSIONS as $type) {
            if ($input->hasParameterOption('--'.$type)) {
                $options->getMainModifiers()->get($type)->update();
            }
        }

        foreach (Version::PRE_RELEASE_VERSIONS as $type) {
            if ($input->hasParameterOption('--'.$type)) {
                $options->getPreReleaseModifiers()->get($type)->enable();
            }
        }

        if ($input->hasParameterOption('--release')) {
            $options->release();
        }

        if ($input->hasParameterOption('--'.Version::META_DATE)) {
            $options->addDateMeta($input->getOption(Version::META_DATE));
        }

        if ($input->hasParameterOption('--'.Version::META)) {
            $options->addMeta($input->getOption(Version::META));
        }

        if ($options->hasPreRelease()) {
            $options->isDowngrade() ? $options->decreasePreRelease() : $options->increasePreRelease();
        }

        return $options;
    }
}
