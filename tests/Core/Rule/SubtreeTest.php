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
use Novactive\eZSmartCacheClearBundle\Core\Rule\Subtree;
use PHPUnit\Framework\TestCase;

class SubtreeTest extends TestCase
{
    /**
     * @dataProvider providerClearCache
     */
    public function testClearCache($ids, $config, $expectedResult)
    {
        $locations = [];
        foreach ($ids as $id) {
            $location     = $this->prophesize(Location::class);
            $location->id = $id;
            $locations[]  = $location;
        }

        $purgeClientMock =
            $this->getMockBuilder(PurgeClientInterface::class)->setMethods(['purge', 'purgeAll'])->getMock();
        $purgeClientMock->expects($this->at(0))->method('purge')->with($expectedResult);

        $locationHandler = $this->prophesize(Handler::class);

        $rule = new Subtree($purgeClientMock, $locationHandler->reveal());
        $rule->clearCache($locations, $config);
    }

    public function providerClearCache()
    {
        return [
            [['6', '55'], ['enabled' => true], ['path-6', 'path-55']],
        ];
    }
}
