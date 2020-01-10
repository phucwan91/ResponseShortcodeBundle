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

namespace Ekino\ResponseShortcodeBundle\EventListener;

use Ekino\ResponseShortcodeBundle\DataCollector\ShortcodeCollector;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeCacheHandlerInterface;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeHandler;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeInterface;
use Ekino\ResponseShortcodeBundle\Service\ShortcodePool;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeValidationInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\KernelEvent;

/**
 * Class ShortcodeListener.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeListener
{
    /**
     * @var string
     */
    private $formatTag;

    /**
     * @var ShortcodePool
     */
    private $shortcodePool;

    /**
     * @var ShortcodeHandler
     */
    private $shortcodeHandler;

    /**
     * @var ShortcodeValidationInterface
     */
    private $shortcodeValidation;

    /**
     * @var ShortcodeCacheHandlerInterface
     */
    private $cacheHandler;

    /**
     * @var ShortcodeCollector
     */
    private $collector;

    /**
     * ShortcodeListener constructor.
     *
     * @param string                         $formatTag
     * @param ShortcodePool                  $shortcodePool
     * @param ShortcodeHandler               $shortcodeHandler
     * @param ShortcodeValidationInterface   $shortcodeValidation
     * @param ShortcodeCacheHandlerInterface $cacheHandler
     * @param ShortcodeCollector             $collector
     */
    public function __construct(
        string $formatTag,
        ShortcodePool $shortcodePool,
        ShortcodeHandler $shortcodeHandler,
        ShortcodeValidationInterface $shortcodeValidation,
        ShortcodeCacheHandlerInterface $cacheHandler,
        ShortcodeCollector $collector
    ) {
        $this->shortcodePool       = $shortcodePool;
        $this->shortcodeHandler    = $shortcodeHandler;
        $this->formatTag           = $formatTag;
        $this->shortcodeValidation = $shortcodeValidation;
        $this->cacheHandler        = $cacheHandler;
        $this->collector           = $collector;
    }

    /**
     * @param KernelEvent $event
     */
    public function onKernelResponse(KernelEvent $event): void
    {
        if (!method_exists($event, 'getResponse')
            || $this->shortcodeValidation->validate($event->getRequest()) === false
        ) {
            return;
        }

        $replacers = [];
        $response  = $event->getResponse();

        if (!$response instanceof Response) {
            return;
        }

        $content   = $response->getContent();
        $tags      = $this->shortcodeHandler->getShortcodeTagsFromContent($content);

        foreach ($tags as $tag) {
            list($tagName, $params) = $this->shortcodeHandler->extractTag($tag);
            $shortcode              = $this->shortcodePool->getShortcode($tagName);

            $this->collector->get($shortcode, $tag);

            if (!$tagName || !$shortcode || ($shortcode instanceof ShortcodeInterface && !$shortcode->isInvoked())) {
                continue;
            }

            if (method_exists($shortcode, 'configureOptions')) {
                $shortcode->configureOptions($params);
            }

            $key             = str_replace(ShortcodeInterface::CONCATENATION_CHAR, $tag, $this->formatTag);
            $replacers[$key] = $this->cacheHandler->retrieveOutput($key, $shortcode->output());
        }

        if (!$replacers) {
            return;
        }

        $response->setContent(str_replace(array_keys($replacers), array_values($replacers), $content));
    }
}
