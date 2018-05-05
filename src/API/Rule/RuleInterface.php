<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\API\Rule;

use eZ\Publish\SPI\Persistence\Content\Location;
use eZ\Publish\SPI\Persistence\Content\Location\Handler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;

/**
 * Cache Clear Rule Interface.
 */
interface RuleInterface
{
    /**
     * Handler constructor.
     *
     * @param PurgeClientInterface $purgeClient
     * @param Handler              $spiLocationHandler
     */
    public function __construct(PurgeClientInterface $purgeClient, Handler $spiLocationHandler);

    /**
     * Clear cache according to the rule configuration provided.
     *
     * @param Location[] $locations
     * @param array      $config
     *
     * @return mixed
     */
    public function clearCache(array $locations, array $config = []);
}
