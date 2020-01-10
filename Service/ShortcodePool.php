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

namespace Ekino\ResponseShortcodeBundle\Service;

/**
 * Class ShortcodePool.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodePool
{
    /**
     * @var array
     */
    private $shortcodes = [];

    /**
     * @param string $type
     *
     * @return ShortcodeInterface|null
     */
    public function getShortcode(string $type): ?ShortcodeInterface
    {
        return $this->shortcodes[$type] ?? null;
    }

    /**
     * @param ShortcodeInterface $shortcode
     *
     * @return self
     */
    public function setShortcode(ShortcodeInterface $shortcode): self
    {
        $this->shortcodes[$shortcode->getTag()] = $shortcode;

        return $this;
    }
}
