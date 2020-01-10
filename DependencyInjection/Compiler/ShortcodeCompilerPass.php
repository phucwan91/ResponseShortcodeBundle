<?php

declare(strict_types=1);

/*
 * This file is part of the ekino/response-shortcode-bundle project.
 *
 * (c) Ekino
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Ekino\ResponseShortcodeBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Reference;

/**
 * Class ShortcodeCompilerPass.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeCompilerPass implements CompilerPassInterface
{
    /**
     * {@inheritdoc}
     */
    public function process(ContainerBuilder $container): void
    {
        // Always first check if the primary service is defined
        if (!$container->has('response_shortcode.pool')) {
            return;
        }

        $definition     = $container->findDefinition('response_shortcode.pool');
        $taggedServices = $container->findTaggedServiceIds('response_shortcode');

        foreach ($taggedServices as $id => $tags) {
            $definition->addMethodCall('setShortcode', [new Reference($id)]);
        }
    }
}
