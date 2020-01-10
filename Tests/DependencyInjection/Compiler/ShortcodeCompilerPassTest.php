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

namespace Ekino\ResponseShortcodeBundle\Tests\DependencyInjection\Compiler;

use Ekino\ResponseShortcodeBundle\DependencyInjection\Compiler\ShortcodeCompilerPass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;

/**
 * Class ShortcodeCompilerPassTest.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeCompilerPassTest extends TestCase
{
    /**
     * Test process method
     *
     * @param bool  $hasId
     * @param array $taggedServices
     *
     * @dataProvider serviceProvider
     */
    public function testProcess(bool $hasId, array $taggedServices): void
    {
        $container  = $this->createMock(ContainerBuilder::class);
        $definition = $this->createMock(Definition::class);
        $compiler   = new ShortcodeCompilerPass();

        $container
            ->expects($this->once())
            ->method('has')
            ->with('response_shortcode.pool')
            ->willReturn($hasId)
        ;
        $container
            ->expects($this->exactly((int) $hasId))
            ->method('findDefinition')
            ->with('response_shortcode.pool')
            ->willReturn($definition)
        ;
        $container
            ->expects($this->exactly((int) $hasId))
            ->method('findTaggedServiceIds')
            ->with('response_shortcode')
            ->willReturn($taggedServices)
        ;
        $definition
            ->expects($this->exactly(\count($taggedServices)))
            ->method('addMethodCall')
            ->with('setShortcode')
            ->willReturnSelf()
        ;

        $compiler->process($container);
    }

    /**
     * @return \Generator
     */
    public function serviceProvider(): \Generator
    {
        yield [false, []];
        yield [true, []];
        yield [true, ['response_shortcode.ex1' => '', 'response_shortcode.ex2' => '']];
    }
}
