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

use Enuage\VersionUpdaterBundle\Command\UpdateVersionCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Component\Console\Tester\ApplicationTester;

/**
 * Class CommandTest
 *
 * @package Tests
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class CommandTest extends FunctionalTestCase
{
    /**
     * @var ApplicationTester $commandTester
     */
    private $commandTester;

    /**
     * @var string $commandName
     */
    private $commandName;

    public function testNothingHappens()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
            ]
        );
    }

    /**
     * @param array $cases
     * @param array $arguments
     * @param array $options
     */
    private function runCommandTest(array $cases, array $arguments = [], array $options = [])
    {
        foreach ($cases as $init => $expected) {
            $this->setTestFileContent($init);
            $this->runCommand($arguments, $options);
            $this->assertContentEqualTo($expected);
            $this->statusQuo();
        }
    }

    /**
     * @param string $data
     *
     * @return void
     */
    private function setTestFileContent(string $data)
    {
        file_put_contents($this->getTestFilePath(), $data);
    }

    /**
     * @return string
     */
    private function getTestFilePath(): string
    {
        return __DIR__.'/support/file.txt';
    }

    /**
     * @param array $arguments
     * @param array $options
     */
    private function runCommand(array $arguments = [], array $options = [])
    {
        $this->commandTester->run(array_merge(['command' => $this->commandName], $arguments), $options);
    }

    /**
     * @param string $expected
     */
    private function assertContentEqualTo(string $expected)
    {
        $this->assertEquals($expected, file_get_contents($this->getTestFilePath()));
    }

    /**
     * @return void
     */
    private function statusQuo()
    {
        $this->setTestFileContent('');
    }

    public function testWithPrefix()
    {
        $this->runCommandTest(
            [
                'version=v0.1.0' => 'version=v0.1.0',
            ]
        );
    }

    public function testSetVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=1.0.0',
            ],
            [
                'version' => '1.0.0',
            ]
        );
    }

    public function testIncreaseMajorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=1.0.0',
                'version=1.1.0' => 'version=2.0.0',
            ],
            [
                '--major' => true,
            ]
        );
    }

    public function testDecreaseMajorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=1.1.0' => 'version=0.1.0',
            ],
            [
                '--major' => true,
                '--down' => true,
            ]
        );
    }

    public function testIncreaseMinorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.2.0',
                'version=0.2.0' => 'version=0.3.0',
            ],
            [
                '--minor' => true,
            ]
        );
    }

    public function testDecreaseMinorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.2.0' => 'version=0.1.0',
            ],
            [
                '--minor' => true,
                '--down' => true,
            ]
        );
    }

    public function testIncreaseMajorAndMinorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=1.1.0',
                'version=1.2.0' => 'version=2.1.0',
            ],
            [
                '--major' => true,
                '--minor' => true,
            ]
        );
    }

    public function testDecreaseMajorAndMinorVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.2.0' => 'version=0.1.0',
                'version=1.1.0' => 'version=0.1.0',
                'version=2.1.0' => 'version=1.0.0',
            ],
            [
                '--major' => true,
                '--minor' => true,
                '--down' => true,
            ]
        );
    }

    public function testIncreasePatchVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.1',
                'version=0.1.1' => 'version=0.1.2',
            ],
            [
                '--patch' => true,
            ]
        );
    }

    public function testDecreasePatchVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.1.1' => 'version=0.1.0',
            ],
            [
                '--patch' => true,
                '--down' => true,
            ]
        );
    }

    public function testDecreaseMajorMinorAndPatchVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.1.1' => 'version=0.1.0',
                'version=1.1.1' => 'version=0.1.0',
                'version=2.1.1' => 'version=1.0.0',
            ],
            [
                '--major' => true,
                '--minor' => true,
                '--patch' => true,
                '--down' => true,
            ]
        );
    }

    public function testDecreaseMinorAndPatchVersion()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.1.1' => 'version=0.1.0',
                'version=0.1.2' => 'version=0.1.1',

                'version=0.2.1' => 'version=0.1.0',
                'version=0.2.2' => 'version=0.1.0',

                'version=1.1.0' => 'version=1.0.0',
                'version=1.1.1' => 'version=1.0.0',
                'version=1.2.1' => 'version=1.1.0',
            ],
            [
                '--minor' => true,
                '--patch' => true,
                '--down' => true,
            ]
        );
    }

    public function testAlphaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0-alpha',
                'version=0.1.0-alpha' => 'version=0.1.0-alpha.1',
                'version=0.1.0-alpha.1' => 'version=0.1.0-alpha.2',
            ],
            [
                '--alpha' => true,
            ]
        );
    }

    public function testDecreaseAlphaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0-alpha' => 'version=0.1.0',
                'version=0.1.0-alpha.1' => 'version=0.1.0-alpha',
            ],
            [
                '--alpha' => true,
                '--down' => true,
            ]
        );
    }

    public function testBetaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0-beta',
                'version=0.1.0-alpha' => 'version=0.1.0-beta',
                'version=0.1.0-beta' => 'version=0.1.0-beta.1',
                'version=0.1.0-beta.1' => 'version=0.1.0-beta.2',
            ],
            [
                '--beta' => true,
            ]
        );
    }

    public function testDecreaseBetaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0-beta' => 'version=0.1.0',
                'version=0.1.0-beta.1' => 'version=0.1.0-beta',
                'version=0.1.0-beta.2' => 'version=0.1.0-beta.1',
            ],
            [
                '--beta' => true,
                '--down' => true,
            ]
        );
    }

    public function testAlphaAndBetaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0-beta',
                'version=0.1.0-alpha' => 'version=0.1.0-beta',
                'version=0.1.0-beta' => 'version=0.1.0-beta.1',
                'version=0.1.0-beta.1' => 'version=0.1.0-beta.2',
            ],
            [
                '--alpha' => true,
                '--beta' => true,
            ]
        );
    }

    public function testDecreaseAlphaAndBetaRelease()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0',
                'version=0.1.0-alpha' => 'version=0.1.0',
                'version=0.1.0-beta' => 'version=0.1.0',
                'version=0.1.0-beta.1' => 'version=0.1.0-beta',
            ],
            [
                '--alpha' => true,
                '--beta' => true,
                '--down' => true,
            ]
        );
    }

    public function testReleaseCandidate()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0-rc',
                'version=0.1.0-alpha' => 'version=0.1.0-rc',
                'version=0.1.0-beta' => 'version=0.1.0-rc',
                'version=0.1.0-rc' => 'version=0.1.0-rc.1',
                'version=0.1.0-rc.1' => 'version=0.1.0-rc.2',
            ],
            [
                '--rc' => true,
            ]
        );
    }

    public function testDecreaseReleaseCandidate()
    {
        $this->runCommandTest(
            [
                'version=0.1.0-rc' => 'version=0.1.0',
                'version=0.1.0-rc.1' => 'version=0.1.0-rc',
                'version=0.1.0-rc.2' => 'version=0.1.0-rc.1',
            ],
            [
                '--rc' => true,
                '--down' => true,
            ]
        );
    }

    public function testAlphaBetaAndReleaseCandidate()
    {
        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0-rc',
                'version=0.1.0-alpha' => 'version=0.1.0-rc',
                'version=0.1.0-beta' => 'version=0.1.0-rc',
                'version=0.1.0-rc' => 'version=0.1.0-rc.1',
                'version=0.1.0-rc.1' => 'version=0.1.0-rc.2',
            ],
            [
                '--alpha' => true,
                '--beta' => true,
                '--rc' => true,
            ]
        );
    }

    public function testMeta()
    {
        $date = date('c');

        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0+'.$date,
            ],
            [
                '--meta' => $date,
            ]
        );
    }

    public function testDate()
    {
        $date = date('U');

        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0+'.$date,
            ],
            [
                '--meta' => $date,
            ]
        );
    }

    public function testMetaAndDate()
    {
        $date = date('U');
        $meta = sha1($date);

        $this->runCommandTest(
            [
                'version=0.1.0' => 'version=0.1.0+'.$date.'+'.$meta,
            ],
            [
                '--date' => 'U',
                '--meta' => $meta,
            ]
        );
    }

    public function testVersionAndMeta()
    {
        $date = date('U');
        $meta = sha1($date);

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--alpha' => true,
                '--date' => 'U',
                '--meta' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-alpha+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--beta' => true,
                '--date' => 'U',
                '--meta' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-beta+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--rc' => true,
                '--date' => 'U',
                '--meta' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-rc+'.$date.'+'.$meta);
        $this->statusQuo();
    }

    public function testDecreaseVersionWithMeta()
    {
        $date = date('U');
        $meta = sha1($date);

        $initialValue = 'version=0.1.0-alpha.1';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--alpha' => true,
                '--date' => 'U',
                '--meta' => $meta,
                '--down' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-alpha+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--beta' => true,
                '--date' => 'U',
                '--meta' => $meta,
                '--down' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-beta+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc.1';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--rc' => true,
                '--date' => 'U',
                '--meta' => $meta,
                '--down' => $meta,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0-rc+'.$date.'+'.$meta);
        $this->statusQuo();
    }

    public function testRelease()
    {
        $initialValue = 'version=0.1.0-alpha+1545264473+c00a683dd4ecfa0fac525675a25c559bf0cda444';
        $this->setTestFileContent($initialValue);
        $this->runCommand(
            [
                '--release' => true,
            ]
        );
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();
    }

    protected function setUp()
    {
        parent::setUp();

        $application = new Application($this->getKernel());
        $command = new UpdateVersionCommand();
        $application->add($command);
        $application->setAutoExit(false);
        $this->commandTester = new ApplicationTester($application);
        $this->commandName = $command->getName();
    }
}
