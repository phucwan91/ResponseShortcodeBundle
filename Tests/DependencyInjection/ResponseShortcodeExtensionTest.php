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

namespace Ekino\ResponseShortcodeBundle\Tests\DependencyInjection;

use Ekino\ResponseShortcodeBundle\DependencyInjection\ResponseShortcodeExtension;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ChildDefinition;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class ResponseShortcodeExtensionTest.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ResponseShortcodeExtensionTest extends TestCase
{
    /**
     * @return void
     */
    public function testLoadConfig(): void
    {
        $configs   = [[
            'cache_handler'        => 'App/Service/CustomCacheHandler',
            'validator'            => 'App/Service/CustomerValidator',
            'excluded_uri_pattern' => ['api/', '_profiler/'],
            'format_tag'           => '[[+]]',
        ]];
        $container       = $this->createMock(ContainerBuilder::class);
        $definition      = $this->createMock(Definition::class);
        $childDefinition = $this->createMock(ChildDefinition::class);
        $extension       = new ResponseShortcodeExtension();

        $container->expects($this->atLeastOnce())->method('setAlias');

        $container->expects($this->once())->method('getDefinition')->with('response_shortcode.validation')->willReturn($definition);
        $container->expects($this->once())->method('setParameter')->with('format_tag', $configs[0]['format_tag'])->willReturn($definition);
        $container->expects($this->once())->method('registerForAutoconfiguration')->with(ShortcodeInterface::class)->willReturn($childDefinition);

        $childDefinition->expects($this->once())->method('addTag')->with('response_shortcode');

        $definition->expects($this->once())->method('setClass')->with($configs[0]['validator'])->willReturnSelf();
        $definition->expects($this->once())->method('setArgument')->with(0, $configs[0]['excluded_uri_pattern'])->willReturnSelf();

        $extension->load($configs, $container);
    }
}
