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
use Novactive\eZSmartCacheClearBundle\Core\Rule\Parents;
use PHPUnit\Framework\TestCase;

class ParentsTest extends TestCase
{
    /**
     * @dataProvider providerClearCache
     */
    public function testClearCache($pathStrings, $config, $expectedResult)
    {
        $locations = [];
        foreach ($pathStrings as $pathString) {
            $location             = $this->prophesize(Location::class);
            $location->pathString = $pathString;
            $locations[]          = $location;
        }

        $purgeClientMock = $this->getMockBuilder(PurgeClientInterface::class)
                                ->setMethods(['purge', 'purgeAll'])
                                ->getMock();
        $purgeClientMock->expects($this->at(0))->method('purge')->with($expectedResult);

        $locationHandler = $this->prophesize(Handler::class);

        $rule = new Parents($purgeClientMock, $locationHandler->reveal());
        $rule->clearCache($locations, $config);
    }

    public function providerClearCache()
    {
        return [
            [['/1/2/3/4/5/6/', '/1/2/25/35/45/55/'], ['enabled'=> true, 'nbLevels' => 2], ['location-5', 'location-4', 'location-45', 'location-35']],
            [['/1/2/3/4/5/6/', '/1/2/25/35/45/55/'], ['enabled'=> true, 'nbLevels' => 3], ['location-5', 'location-4', 'location-3', 'location-45', 'location-35', 'location-25']],
            [['/1/2/3/4/5/6/', '/1/2/25/35/45/55/'], ['enabled'=> true], ['location-5', 'location-45']]
        ];
    }
}
