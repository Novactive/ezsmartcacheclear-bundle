<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\Tests\Core\Rule;

use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Novactive\eZSmartCacheClearBundle\Core\Rule\Siblings;
use PHPUnit\Framework\TestCase;

class SiblingsTest extends TestCase
{
    /**
     * @dataProvider providerClearCache
     */
    public function testClearCache($parentIds, $config, $expectedResult)
    {
        $locations = [];
        foreach ($parentIds as $parentId) {
            $location           = $this->prophesize(Location::class);
            $location->parentId = $parentId;
            $locations[]        = $location;
        }

        $purgeClientMock =
            $this->getMockBuilder(PurgeClientInterface::class)->setMethods(['purge', 'purgeAll'])->getMock();
        $purgeClientMock->expects($this->at(0))->method('purge')->with($expectedResult);

        $locationHandler = $this->prophesize(Handler::class);

        $rule = new Siblings($purgeClientMock, $locationHandler->reveal());
        $rule->clearCache($locations, $config);
    }

    public function providerClearCache()
    {
        return [
            [['5', '45'], ['enabled' => true], ['parent-5', 'parent-45']],
        ];
    }
}
