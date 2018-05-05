<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\Core\Slot;

use eZ\Publish\Core\SignalSlot\Signal;
use eZ\Publish\Core\SignalSlot\Slot;
use eZ\Publish\SPI\Persistence\Content\Type;
use eZ\Publish\SPI\Persistence\Handler;
use Novactive\eZSmartCacheClearBundle\API\Rule\RuleInterface;

/**
 * Publish Slot Class.
 */
class Publish extends Slot
{
    /**
     * @var \eZ\Publish\SPI\Persistence\Handler
     */
    protected $persistenceHandler;

    /**
     * @var RuleInterface[]
     */
    protected $cacheClearRules = [];

    /**
     * @var array
     */
    protected $config;

    /**
     * Publish constructor.
     *
     * @param Handler $persistenceHandler
     * @param array   $config
     */
    public function __construct(Handler $persistenceHandler, array $config)
    {
        $this->persistenceHandler = $persistenceHandler;
        $this->config             = $config;
    }

    /**
     * Add Cache Clear Rule.
     *
     * @param RuleInterface $rule
     * @param string        $ruleName
     */
    public function addCacheClearRule(RuleInterface $rule, $ruleName)
    {
        $this->cacheClearRules[$ruleName] = $rule;
    }

    /**
     * Invalidate parent location cache when a new location is created.
     *
     * @param \eZ\Publish\Core\SignalSlot\Signal $signal
     */
    public function receive(Signal $signal)
    {
        if (!$signal instanceof Signal\ContentService\PublishVersionSignal) {
            return;
        }

        $content     = $this->persistenceHandler->contentHandler()->load($signal->contentId, $signal->versionNo);
        $contentType = $this->persistenceHandler->contentTypeHandler()->load(
            $content->versionInfo->contentInfo->contentTypeId
        );
        $locations   = $this->persistenceHandler->locationHandler()->loadLocationsByContent(
            $signal->contentId
        );

        foreach ($this->getContentTypeRules($contentType) as $ruleName => $ruleConfig) {
            if (true === $ruleConfig['enabled']) {
                $this->cacheClearRules[$ruleName]->clearCache($locations, $ruleConfig);
            }
        }
    }

    /**
     * Get content type rules from config.
     *
     * @param string $contentType
     *
     * @return array
     */
    private function getContentTypeRules(Type $contentType)
    {
        $rules = [];
        foreach ($this->config as $config) {
            if ($config['content_type'] == $contentType->identifier) {
                $rules = $config['rules'];
            }
        }

        return $rules;
    }
}
