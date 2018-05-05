<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle\DependencyInjection\CompilerPass;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Compiler Pass class to inject Rules into Slots Services.
 */
class CacheClearRulesPass implements CompilerPassInterface
{
    /**
     * Process the configuration.
     *
     * @param ContainerBuilder $container
     */
    public function process(ContainerBuilder $container)
    {
        $slots = $this->getSlots($container);
        $rules = $this->getRules($container);

        foreach ($slots as $slot) {
            foreach ($rules as $ruleName => $rule) {
                $slot->addMethodCall(
                    'addCacheClearRule',
                    [$rule, $ruleName]
                );
            }
        }
    }

    /**
     * Retrieve Smart Cache Clear Slots.
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getSlots(ContainerBuilder $container)
    {
        $taggedSlotServices = $container->findTaggedServiceIds(
            'ez_smart_cache_clear.slot'
        );
        $slots              = [];
        foreach ($taggedSlotServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $slots[$attributes['signal']] = $container->findDefinition($id);
            }
        }

        return $slots;
    }

    /**
     * Retrieve Smart Cache Clear Rules.
     *
     * @param ContainerBuilder $container
     *
     * @return array
     */
    private function getRules(ContainerBuilder $container)
    {
        $taggedRuleServices = $container->findTaggedServiceIds(
            'ez_smart_cache_clear.rule'
        );
        $rules              = [];
        foreach ($taggedRuleServices as $id => $tags) {
            foreach ($tags as $attributes) {
                $rules[$attributes['ruleName']] = new Reference($id);
            }
        }

        return $rules;
    }
}
