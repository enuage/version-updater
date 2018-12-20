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
        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run(['command' => $this->commandName]);
        $this->assertContentEqualTo($initialValue);
        $this->statusQuo();
    }

    /**
     * @param string $data
     *
     * @return void
     */
    private function setTestFileContent(string $data): void
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
     * @param string $expected
     */
    private function assertContentEqualTo(string $expected)
    {
        $this->assertEquals($expected, file_get_contents($this->getTestFilePath()));
    }

    /**
     * @return void
     */
    private function statusQuo(): void
    {
        $this->setTestFileContent('');
    }

    public function testSetVersion()
    {
        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run(
            [
                'command' => $this->commandName,
                'version' => '1.0.0',
            ]
        );
        $this->assertContentEqualTo('version=1.0.0');
        $this->statusQuo();
    }

    public function testIncreaseMajorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--major' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=1.0.0');
        $this->statusQuo();

        $initialValue = 'version=1.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=2.0.0');
        $this->statusQuo();
    }

    public function testDecreaseMajorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--major' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo($initialValue);
        $this->statusQuo();

        $initialValue = 'version=1.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();
    }

    public function testIncreaseMinorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--minor' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.2.0');
        $this->statusQuo();

        $initialValue = 'version=0.2.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.3.0');
        $this->statusQuo();
    }

    public function testDecreaseMinorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--minor' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo($initialValue);
        $this->statusQuo();

        $initialValue = 'version=0.2.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();
    }

    public function testIncreaseMajorAndMinorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--major' => true,
            '--minor' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=1.1.0');
        $this->statusQuo();

        $initialValue = 'version=1.2.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=2.1.0');
        $this->statusQuo();
    }

    public function testDecreaseMajorAndMinorVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--major' => true,
            '--minor' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo($initialValue);
        $this->statusQuo();

        $initialValue = 'version=0.2.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=1.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=2.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=1.0.0');
        $this->statusQuo();
    }

    public function testIncreasePatchVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--patch' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.2');
        $this->statusQuo();
    }

    public function testDecreasePatchVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--patch' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();
    }

    public function testDecreaseMajorMinorAndPatchVersion()
    {
        $input = [
            'command' => $this->commandName,
            '--major' => true,
            '--minor' => true,
            '--patch' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=1.1.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=2.1.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=1.0.0');
        $this->statusQuo();
    }

    public function testAlphaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--alpha' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-alpha');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-alpha.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-alpha.2');
        $this->statusQuo();
    }

    public function testDecreaseAlphaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--alpha' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-alpha');
        $this->statusQuo();
    }

    public function testBetaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--beta' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta.2');
        $this->statusQuo();
    }

    public function testDecreaseBetaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--beta' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();
    }

    public function testAlphaAndBetaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--alpha' => true,
            '--beta' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta.2');
        $this->statusQuo();
    }

    public function testDecreaseAlphaAndBetaRelease()
    {
        $input = [
            'command' => $this->commandName,
            '--alpha' => true,
            '--beta' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-beta');
        $this->statusQuo();
    }

    public function testReleaseCandidate()
    {
        $input = [
            'command' => $this->commandName,
            '--rc' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc.2');
        $this->statusQuo();
    }

    public function testDecreaseReleaseCandidate()
    {
        $input = [
            'command' => $this->commandName,
            '--rc' => true,
            '--down' => true,
        ];

        $initialValue = 'version=0.1.0-rc';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();
    }

    public function testAlphaBetaAndReleaseCandidate()
    {
        $input = [
            'command' => $this->commandName,
            '--alpha' => true,
            '--beta' => true,
            '--rc' => true,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-alpha';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc.1');
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0-rc.2');
        $this->statusQuo();
    }

    public function testMeta()
    {
        $date = date('c');
        $input = [
            'command' => $this->commandName,
            '--meta' => date('c'),
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0+'.$date);
        $this->statusQuo();
    }

    public function testDate()
    {
        $date = date('U');
        $input = [
            'command' => $this->commandName,
            '--date' => 'U',
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0+'.$date);
        $this->statusQuo();
    }

    public function testMetaAndDate()
    {
        $date = date('U');
        $meta = sha1($date);
        $input = [
            'command' => $this->commandName,
            '--date' => 'U',
            '--meta' => $meta,
        ];

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run($input);
        $this->assertContentEqualTo('version=0.1.0+'.$date.'+'.$meta);
        $this->statusQuo();
    }

    public function testVersionAndMeta()
    {
        $date = date('U');
        $meta = sha1($date);

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--alpha' => true,
            '--date' => 'U',
            '--meta' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-alpha+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--beta' => true,
            '--date' => 'U',
            '--meta' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-beta+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--rc' => true,
            '--date' => 'U',
            '--meta' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-rc+'.$date.'+'.$meta);
        $this->statusQuo();
    }

    public function testDecreaseVersionWithMeta()
    {
        $date = date('U');
        $meta = sha1($date);

        $initialValue = 'version=0.1.0-alpha.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--alpha' => true,
            '--date' => 'U',
            '--meta' => $meta,
            '--down' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-alpha+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0-beta.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--beta' => true,
            '--date' => 'U',
            '--meta' => $meta,
            '--down' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-beta+'.$date.'+'.$meta);
        $this->statusQuo();

        $initialValue = 'version=0.1.0-rc.1';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--rc' => true,
            '--date' => 'U',
            '--meta' => $meta,
            '--down' => $meta,
        ]);
        $this->assertContentEqualTo('version=0.1.0-rc+'.$date.'+'.$meta);
        $this->statusQuo();
    }

    public function testRelease()
    {
        $initialValue = 'version=0.1.0-alpha+1545264473+c00a683dd4ecfa0fac525675a25c559bf0cda444';
        $this->setTestFileContent($initialValue);
        $this->commandTester->run([
            'command' => $this->commandName,
            '--release' => true,
        ]);
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
