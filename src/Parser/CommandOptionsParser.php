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
     */
    public static function parse(InputInterface $input): VersionOptions
    {
        $options = new VersionOptions();

        $options->set('version', $input->getArgument('version'));

        foreach (VersionOptions::OPTIONS as $option) {
            $options->set($option, $input->hasParameterOption('--'.$option));
        }

        if ($options->isDateDefined()) {
            $options->setDateFormat($input->getOption('date'));
        }

        if ($options->isMetaDefined()) {
            $options->setMetaValue($input->getOption('meta'));
        }

        if ($options->hasPreRelease() && $options->isDowngrade()) {
            $options->decreasePreRelease();
        }

        return $options;
    }
}
