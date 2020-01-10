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

use Ekino\ResponseShortcodeBundle\Service\ShortcodeInterface;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;

/**
 * Class ResponseShortcodeExtension.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ResponseShortcodeExtension extends Extension
{
    /**
     * {@inheritdoc}
     */
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config        = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\XmlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.xml');

        if ($config['cache_handler']) {
            $container->setAlias('response_shortcode.cache_handler_alias', $config['cache_handler']);
        }

        $container->getDefinition('response_shortcode.validation')->setClass($config['validator'])->setArgument(0, $config['excluded_uri_pattern']);
        $container->registerForAutoconfiguration(ShortcodeInterface::class)->addTag('response_shortcode');
        $container->setParameter('format_tag', $config['format_tag']);
    }

    /**
     * @inheritDoc
     */
    public function getAlias()
    {
        return 'ekino_response_shortcode';
    }
}
