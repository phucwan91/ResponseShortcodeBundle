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

namespace Ekino\ResponseShortcodeBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * Class Configuration.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        // Keep compatibility with symfony/config < 4.2
        if (!method_exists(TreeBuilder::class, 'getRootNode')) {
            $treeBuilder = new TreeBuilder();
            $rootNode    = $treeBuilder->root('ekino_response_shortcode');
        } else {
            $treeBuilder = new TreeBuilder('ekino_response_shortcode');
            $rootNode    = $treeBuilder->getRootNode();
        }

        $rootNode
            ->children()
                ->arrayNode('excluded_uri_pattern')
                    ->info('Define uri which will be ignored by shortcodes')
                    ->example(['^/media/cache(.*)', '- ^/uploads/(.*)', '^/monitor(.*)'])
                    ->defaultValue([])
                    ->prototype('scalar')->end()
                ->end()
                ->scalarNode('format_tag')
                    ->info('Define a tag of the shortcode')
                    ->example('[[+]]')
                    ->defaultValue('[[+]]')
                    ->beforeNormalization()
                        ->ifTrue(function ($v) { return strpos($v, '+') === false || \count(explode('+', $v)) !== 2 || preg_match('/[A-Za-z0-9]/', $v) > 0;})
                        ->thenInvalid('"+" must be included between the open tag and the close tag in the given format tag: %s. Open tag and close tag accept any special characters, except the plus "+" character. Valid format example: [ [ + ] ]')
                    ->end()
                ->end()
                ->scalarNode('validator')
                    ->info('Define a custom validator')
                    ->defaultValue('Ekino\ResponseShortcodeBundle\Service\ShortcodeValidation')
                ->end()
                ->scalarNode('cache_handler')
                    ->info('Define a customer cache handler')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
