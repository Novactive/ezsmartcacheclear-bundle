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
 * Parents Cache Clear Rule.
 */
class Parents extends AbstractRule
{
    /**
     * {@inheritdoc}
     */
    public function clearCache(array $locations, array $config = [])
    {
        $nbParentLevels = isset($config['nbLevels']) ? (int) $config['nbLevels'] : 1;

        $tags = [];
        foreach ($locations as $location) {
            $path = array_slice(array_reverse(explode('/', $location->pathString)), 2);
            for ($i = 0; $i < $nbParentLevels; ++$i) {
                $tags[] = 'location-'.$path[$i];
            }
        }

        $this->purgeClient->purge($tags);
    }
}
