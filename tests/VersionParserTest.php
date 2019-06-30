<?php
/**
 * VersionParserTest
 *
 * Created at 2019-06-30 4:43 PM
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

use Enuage\VersionUpdaterBundle\Parser\VersionParser;
use Enuage\VersionUpdaterBundle\ValueObject\Version;

/**
 * Class VersionParserTest
 *
 * @author Serghei Niculaev <spam312sn@gmail.com>
 */
class VersionParserTest extends FunctionalTestCase
{
    public function testParse()
    {
        $version = (new VersionParser('1'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(0, $version->getMinor());
        $this->assertEquals(0, $version->getPatch());

        $version = (new VersionParser('01'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(0, $version->getMinor());
        $this->assertEquals(0, $version->getPatch());

        $version = (new VersionParser('1.1'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(0, $version->getPatch());

        $version = (new VersionParser('1.1.2'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(2, $version->getPatch());

        $version = (new VersionParser('1.1.2-alpha'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(2, $version->getPatch());
        $this->assertEquals(Version::ALPHA, $version->getPreRelease());

        $version = (new VersionParser('1.1.2-alpha.3'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(2, $version->getPatch());
        $this->assertEquals(Version::ALPHA, $version->getPreRelease());
        $this->assertEquals(3, $version->getPreReleaseVersion());

        $version = (new VersionParser('-1.1.2-alpha.3'))->parse();
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(2, $version->getPatch());
        $this->assertEquals(Version::ALPHA, $version->getPreRelease());
        $this->assertEquals(3, $version->getPreReleaseVersion());

        $version = (new VersionParser('v1.1.2-alpha.3'))->parse();
        $this->assertEquals('v', $version->getPrefix());
        $this->assertEquals(1, $version->getMajor());
        $this->assertEquals(1, $version->getMinor());
        $this->assertEquals(2, $version->getPatch());
        $this->assertEquals(Version::ALPHA, $version->getPreRelease());
        $this->assertEquals(3, $version->getPreReleaseVersion());
    }
}
