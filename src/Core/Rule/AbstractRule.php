<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\Core\Rule;

use eZ\Publish\SPI\Persistence\Content\Location\Handler;
use EzSystems\PlatformHttpCacheBundle\PurgeClient\PurgeClientInterface;
use Novactive\eZSmartCacheClearBundle\API\Rule\RuleInterface;

/**
 * Abstract Rule.
 */
abstract class AbstractRule implements RuleInterface
{
    /**
     * @var PurgeClientInterface
     */
    protected $purgeClient;

    /**
     * @var Handler
     */
    protected $locationHandler;

    /**
     * {@inheritdoc}
     */
    public function __construct(PurgeClientInterface $purgeClient, Handler $spiLocationHandler)
    {
        $this->purgeClient     = $purgeClient;
        $this->locationHandler = $spiLocationHandler;
    }

    /**
     * {@inheritdoc}
     */
    abstract public function clearCache(array $locations, array $config = []);
}
