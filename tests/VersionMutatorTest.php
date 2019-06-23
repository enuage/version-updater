<?php
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

use Enuage\VersionUpdaterBundle\DTO\VersionOptions;
use Enuage\VersionUpdaterBundle\Formatter\VersionFormatter;
use Enuage\VersionUpdaterBundle\Mutator\VersionMutator;
use Enuage\VersionUpdaterBundle\Service\VersionService;
use Exception;

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

    /**
     * @throws Exception
     */
    public function testUpdateMajorVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateMajor(true);
        $this->assertVersions('2.0.0', $this->service->update('1', $versionOptions));

        $versionOptions->downgrade();
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

    /**
     * @throws Exception
     */
    public function testUpdateMinorVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateMinor(true);
        $this->assertVersions('1.1.0', $this->service->update('1', $versionOptions));

        $versionOptions->downgrade();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));
    }

    /**
     * @throws Exception
     */
    public function testUpdatePatchVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updatePatch(true);
        $this->assertVersions('1.0.1', $this->service->update('1', $versionOptions));

        $versionOptions->downgrade();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));
    }

    /**
     * @throws Exception
     */
    public function testUpdateAlpha()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateAlpha(true);
        $this->assertVersions('1.0.0-alpha', $this->service->update('1', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1-alpha', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-alpha', $this->service->update('1-alpha.1', $versionOptions));
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1-alpha.2', $versionOptions));
    }

    /**
     * @throws Exception
     */
    public function testUpdateBeta()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateBeta(true);
        $this->assertVersions('1.0.0-beta', $this->service->update('1', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-beta.1', $this->service->update('1-beta', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-beta', $this->service->update('1-beta.1', $versionOptions));
        $this->assertVersions('1.0.0-beta.1', $this->service->update('1-beta.2', $versionOptions));
    }

    /**
     * @throws Exception
     */
    public function testUpdateReleaseCandidate()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->updateReleaseCandidate(true);
        $this->assertVersions('1.0.0-rc', $this->service->update('1', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0', $this->service->update('1', $versionOptions));

        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-rc.1', $this->service->update('1-rc', $versionOptions));

        $versionOptions->decreasePreRelease();
        $this->assertVersions('1.0.0-rc', $this->service->update('1-rc.1', $versionOptions));
        $this->assertVersions('1.0.0-rc.1', $this->service->update('1-rc.2', $versionOptions));
    }

    /**
     * @throws Exception
     */
    public function testIncreasePreReleaseVersion()
    {
        $versionOptions = new VersionOptions();
        $versionOptions->increasePreRelease();
        $this->assertVersions('1.0.0-alpha.1', $this->service->update('1.0.0-alpha', $versionOptions));
        $this->assertVersions('1.0.0-alpha.2', $this->service->update('1.0.0-alpha.1', $versionOptions));

        $this->assertVersions('1.0.0-beta.1', $this->service->update('1.0.0-beta', $versionOptions));

        $this->assertVersions('1.0.0-rc.1', $this->service->update('1.0.0-rc', $versionOptions));

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

    /**
     * {@inheritDoc}
     */
    protected function setUp()
    {
        parent::setUp();

        $this->service = new VersionService();
    }
}
