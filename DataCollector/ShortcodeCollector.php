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

namespace Ekino\ResponseShortcodeBundle\DataCollector;

use Ekino\ResponseShortcodeBundle\Service\ShortcodeInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;

/**
 * Class ShortcodeCollector.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeCollector extends DataCollector
{
    private const VALID_KEY   = 'valid';
    private const INVALID_KEY = 'invalid';
    private const REFUSED_KEY = 'refused';

    /**
     * ShortcodeCollector constructor.
     */
    public function __construct()
    {
        $this->reset();
    }

    /**
     * @inheritDoc
     */
    public function collect(Request $request, Response $response, \Exception $exception = null)
    {
    }

    /**
     * @inheritDoc
     */
    public function getName()
    {
        return 'shortcode';
    }

    /**
     * @inheritDoc
     */
    public function reset()
    {
        $this->data = [
            static::VALID_KEY   => [],
            static::INVALID_KEY => [],
            static::REFUSED_KEY => [],
        ];
    }

    /**
     * @param ShortcodeInterface|null $shortcode
     * @param string                  $tag
     */
    public function get(?ShortcodeInterface $shortcode, string $tag): void
    {
        if ($shortcode instanceof ShortcodeInterface) {
            if ($shortcode->isInvoked()) {
                $this->count($tag, static::VALID_KEY);
            } else {
                $this->count($tag, static::REFUSED_KEY);
            }
        } else {
            $this->count($tag, static::INVALID_KEY);
        }
    }

    /**
     * @return array
     */
    public function getData(): array
    {
        return $this->data;
    }

    /**
     * @return int
     */
    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->data as $data) {
            $total += array_sum($data);
        }

        return $total;
    }

    /**
     * @param string $tag
     * @param string $type
     */
    private function count(string $tag, string $type): void
    {
        $this->data[$type][$tag] = \array_key_exists($tag, $this->data[$type]) ? $this->data[$type][$tag] += 1 : 1;
    }
}
