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

/**
 * Siblings Cache Clear Rule.
 */
class Siblings extends AbstractRule
{
    /**
     * {@inheritdoc}
     */
    public function clearCache(array $locations, array $config = [])
    {
        $tags = [];
        foreach ($locations as $location) {
            $tags[] = 'parent-'.$location->parentId;
        }

        $this->purgeClient->purge($tags);
    }
}
