<?php
/**
 * File part of the eZSmartCacheClearBundle package.
 *
 * @package   Novactive/eZSmartCacheCLearBundle
 *
 * @copyright 2018 Novactive
 * @license   https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace Novactive\eZSmartCacheClearBundle;

use Novactive\eZSmartCacheClearBundle\DependencyInjection\CompilerPass\CacheClearRulesPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Bundle definition class.
 */
class NovaEzSmartCacheClearBundle extends Bundle
{
    /**
     * {@inheritdoc}
     */
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new CacheClearRulesPass());
    }
}
