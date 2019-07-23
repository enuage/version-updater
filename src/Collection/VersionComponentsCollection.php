<?php
/**
 * VersionComponentsCollection
 *
 * Created at 2019-06-30 3:47 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Collection;

use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Enuage\VersionUpdaterBundle\ValueObject\VersionComponent;

/**
 * Class VersionComponentsCollection
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 *
 * @method VersionComponent get($key, $default = null)
 */
class VersionComponentsCollection extends ArrayCollection
{
    const DISABLE_ALL = false;
    const ENABLE_ALL = true;

    /**
     * {@inheritDoc}
     */
    public function __construct(array $types = [], bool $enableAll = self::ENABLE_ALL)
    {
        parent::__construct();

        foreach ($types as $type) {
            parent::set($type, new VersionComponent(true === $enableAll));
        }
    }

    /**
     * @param $type
     * @param $value
     */
    public function set($type, $value)
    {
        if (Version::MAJOR === $type && $value > 0) {
            $this->get(Version::MINOR)->setValue(0);
            $this->get(Version::PATCH)->setValue(0);
        }

        if (Version::MINOR === $type) {
            if (0 === $value && 0 === $this->get(Version::MAJOR)->getValue()) {
                $value = 1;
            }

            if ($this->get(Version::MINOR)->getValue() !== $value) {
                $this->get(Version::PATCH)->setValue(0);
            }
        }

        $this->get($type)->setValue($value);
    }

    public function disableAll()
    {
        /** @var VersionComponent $type */
        foreach ($this->getIterator() as $type) {
            $type->setEnabled(false);
        }
    }

    /**
     * @return array
     */
    public function getValues(): array
    {
        $values = [];
        /** @var VersionComponent $component */
        foreach ($this->getIterator() as $key => $component) {
            $values[$key] = $component->getValue();
        }

        return $values;
    }
}
