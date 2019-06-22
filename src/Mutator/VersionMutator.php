<?php
/**
 * VersionMutator
 *
 * Created at 2019-06-22 2:24 AM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Mutator;

use DateTime;
use Enuage\VersionUpdaterBundle\ValueObject\Version;
use Exception;

/**
 * Class VersionMutator
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionMutator
{
    /**
     * @var Version
     */
    private $version;

    /**
     * @var bool
     */
    private $down = false;

    /**
     * VersionMutator constructor.
     *
     * @param Version $version
     */
    public function __construct(Version $version)
    {
        $this->version = $version;
    }

    /**
     * @param string $key
     */
    public function updateVersion(string $key)
    {
        $value = $this->version->getVersion($key);

        if ($this->isDown()) {
            $value > 0 ? $value-- : $value = 0;
        } else {
            $value++;
        }

        $this->version->setVersion($key, $value);
    }

    /**
     * @return bool
     */
    private function isDown(): bool
    {
        return $this->down;
    }

    /**
     * @param array $preReleaseOptions
     */
    public function updatePreRelease(array $preReleaseOptions)
    {
        $preRelease = $this->version->getPreRelease();
        $this->version->clearPreRelease();

        $preReleaseVersion = $this->version->getPreReleaseVersion();
        $this->version->clearPreReleaseVersion();
        $isPreReleaseVersionDefined = null !== $preReleaseVersion && is_numeric($preReleaseVersion);

        foreach ($preReleaseOptions as $preReleaseOption => $isOptionEnabled) {
            $isPreReleaseDefined = $preRelease === $preReleaseOption;

            if ($isOptionEnabled) {
                $this->version->clearPreRelease()->clearPreReleaseVersion()->setPreRelease($preReleaseOption);

                if (!$isPreReleaseDefined && $this->isDown()) {
                    $this->version->clearPreRelease();
                }

                if ($isPreReleaseDefined && !$isPreReleaseVersionDefined) {
                    if ($this->isDown()) {
                        $this->version->setPreRelease($preReleaseOption, false)->clearPreReleaseVersion();
                    } else {
                        $this->version->setPreReleaseVersion(1);
                    }
                }

                if ($isPreReleaseDefined && $isPreReleaseVersionDefined) {
                    $newPreReleaseVersion = $this->isDown() ? --$preReleaseVersion : ++$preReleaseVersion;
                    if ($newPreReleaseVersion) {
                        $this->version->setPreReleaseVersion($newPreReleaseVersion);
                    }
                }
            }
        }
    }

    public function release()
    {
        $this->version->clearPreRelease()->clearPreReleaseVersion();
    }

    /**
     * @param bool $down
     *
     * @return VersionMutator
     */
    public function setDown(bool $down): self
    {
        $this->down = $down;

        return $this;
    }

    /**
     * @param string $format
     *
     * @return VersionMutator
     * @throws Exception
     */
    public function enableDateMeta(string $format): self
    {
        $this->version->setDateMeta(true);
        $this->version->setDateMetaValue(new DateTime());
        $this->version->setDateMetaFormat($format);

        return $this;
    }

    /**
     * @param string $value
     *
     * @return VersionMutator
     */
    public function enableMeta(string $value): self
    {
        $this->version->setMeta(true);
        $this->version->setMetaValue($value);

        return $this;
    }

    /**
     * @return Version
     */
    public function getVersion(): Version
    {
        return $this->version;
    }
}
