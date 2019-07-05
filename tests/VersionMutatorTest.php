<?php /** @noinspection PhpUnhandledExceptionInspection */
/**
 * VersionMutatorTest
 *
 * Created at 2019-06-23 3:33 PM
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 * @license GNU GPLv3 <https://www.gnu.org/licenses/gpl-3.0.en.html>
 *
 * This file is a part of éNuage version updater command
 *
 * For the full copyright and license information, please view the LICENSE.md
 * file that was distributed with this source code.
 */

namespace Enuage\VersionUpdaterBundle\Tests;

use DateTime;
use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Service\VersionService;

/**
 * Class VersionMutatorTest
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionMutatorTest extends FunctionalTestCase
{
    /**
     * @var VersionService
     */
    private $service;

    public function testUpdateMajorVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increaseMajor();
        $this->assertVersions('2.0.0', $this->service->update('1', $versionOptions));

        $versionOptions->decreaseMajor();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreaseMajor();
        $this->assertVersions('0.1.0', $this->service->update('1', $versionOptions));
    }

    /**
     * @param string $expected
     * @param VersionMutator $versionMutator
     */
    private function assertVersions(string $expected, VersionMutator $versionMutator)
    {
        $versionFormatter = new VersionFormatter();
        $versionFormatter->setMutator($versionMutator);

        $this->assertEquals($expected, $versionFormatter->format());
    }

    public function testUpdateMinorVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increaseMinor();
        $this->assertVersions('1.1.0', $this->service->update('1', $versionOptions));

        $versionOptions->decreaseMinor();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));
    }

    public function testUpdatePatchVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increasePatch();
        $this->assertVersions('1.0.1', $this->service->update('1', $versionOptions));

        $versionOptions->decreasePatch();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));
    }

    public function testUpdateAlpha()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateAlpha();
        $this->assertVersions('1.0.0-alpha', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-alpha', $this->service->update('1-alpha.1', $versionOptions));
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1-alpha.2', $versionOptions));
    }

    public function testUpdateBeta()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateBeta();
        $this->assertVersions('1.0.0-beta', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-beta.1', $this->service->update('1-beta', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-beta', $this->service->update('1-beta.1', $versionOptions));
        $this->assertVersions('1.0.0-beta.1', $this->service->update('1-beta.2', $versionOptions));
    }

    public function testUpdateReleaseCandidate()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateReleaseCandidate();
        $this->assertVersions('1.0.0-rc', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-rc.1', $this->service->update('1-rc', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-rc', $this->service->update('1-rc.1', $versionOptions));
        $this->assertVersions('1.0.0-rc.1', $this->service->update('1-rc.2', $versionOptions));
    }

    public function testIncreasePreReleaseVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1.0.0-alpha', $versionOptions));
        $this->assertVersions('1.0.0-alpha.2', $this->service->update('1.0.0-alpha.1', $versionOptions));

        $this->assertVersions('1.0.0-beta.1', $this->service->update('1.0.0-beta', $versionOptions));

        $this->assertVersions('1.0.0-rc.1', $this->service->update('1.0.0-rc', $versionOptions));
    }

    public function testDecreasePreReleaseVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1.0.0-alpha.2', $versionOptions));
        $this->assertVersions('1.0.0-beta.1', $this->service->update('1.0.0-beta.2', $versionOptions));
        $this->assertVersions('1.0.0-rc.1', $this->service->update('1.0.0-rc.2', $versionOptions));

        $this->assertVersions('1.0.0-alpha', $this->service->update('1.0.0-alpha.1', $versionOptions));
        $this->assertVersions('1.0.0-beta', $this->service->update('1.0.0-beta.1', $versionOptions));
        $this->assertVersions('1.0.0-rc', $this->service->update('1.0.0-rc.1', $versionOptions));

        $this->assertVersions('1.0.0', $this->service->update('1.0.0-alpha', $versionOptions));
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-beta', $versionOptions));
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-rc', $versionOptions));
    }

    public function testPreReleaseVersionModifications()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1.0.0-alpha.2', $versionOptions));
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.2', $this->service->update('1.0.0-alpha.2', $versionOptions));
    }

    public function testRelease()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->release();
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-alpha', $versionOptions));
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-alpha.1', $versionOptions));

        $this->assertVersions('1.0.0', $this->service->update('1.0.0-beta', $versionOptions));
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-beta.1', $versionOptions));

        $this->assertVersions('1.0.0', $this->service->update('1.0.0-rc', $versionOptions));
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-rc.1', $versionOptions));
    }

    public function testVersionMultipleModifications()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increaseMajor()->decreasePatch();
        $this->assertVersions('2.0.0', $this->service->update('1.0.0', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increaseMajor()->increaseMajor()->decreaseMinor();
        $this->assertVersions('2.0.0', $this->service->update('1.0.0', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increaseMajor()->increaseMajor()->decreaseMinor()->increasePatch();
        $this->assertVersions('2.0.1', $this->service->update('1.0.0', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreaseMajor()->increaseMajor()->decreaseMinor()->increasePatch();
        $this->assertVersions('1.0.1', $this->service->update('1.0.0', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease()->increasePreRelease()->decreasePreRelease()->increasePreRelease();
        $this->assertVersions('1.0.0-alpha', $this->service->update('1.0.0-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->decreasePreRelease()->increasePreRelease()->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1.0.0-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease()->decreasePreRelease()->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1.0.0-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->updateAlpha()->decreasePreRelease()->increasePreRelease();
        $this->assertVersions('1.0.0-alpha', $this->service->update('1.0.0-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->updateBeta()->decreasePreRelease()->increasePreRelease();
        $this->assertVersions('1.0.0-beta', $this->service->update('1.0.0-alpha', $versionOptions));

        $versionOptions = new VersionOptions();
        $versionOptions->updateBeta()->increasePreRelease();
        $this->assertVersions('1.0.0-beta', $this->service->update('1.0.0-alpha', $versionOptions));
    }

    public function testAddMeta()
    {
        $date = new DateTime();
        $versionOptions = new VersionOptions();
        $versionOptions->addDateMeta();
        $this->assertVersions('1.0.0+'.$date->format('c'), $this->service->update('1.0.0', $versionOptions));

        $date = new DateTime();
        $versionOptions = new VersionOptions();
        $versionOptions->addDateMeta('U');
        $this->assertVersions('1.0.0+'.$date->format('U'), $this->service->update('1.0.0', $versionOptions));

        $meta = md5('test');
        $versionOptions = new VersionOptions();
        $versionOptions->addMeta($meta);
        $this->assertVersions('1.0.0+'.$meta, $this->service->update('1.0.0', $versionOptions));

        $meta = sha1('test');
        $versionOptions = new VersionOptions();
        $versionOptions->addMeta($meta);
        $this->assertVersions('1.0.0+'.$meta, $this->service->update('1.0.0', $versionOptions));

        $date = new DateTime();
        $meta = sha1('meta');
        $versionOptions = new VersionOptions();
        $versionOptions->addDateMeta('U');
        $versionOptions->addMeta($meta);
        $this->assertVersions('1.0.0+'.$date->format('U').'+'.$meta, $this->service->update('1.0.0', $versionOptions));
    }

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new VersionService();
    }
}
