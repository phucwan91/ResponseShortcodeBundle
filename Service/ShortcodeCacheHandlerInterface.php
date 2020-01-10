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
 * Interface ShortcodeCacheHandlerInterface.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
interface ShortcodeCacheHandlerInterface
{
    /**
     * @param string $key
     * @param string $output
     *
     * @return string
     */
    public function retrieveOutput(string $key, string $output): string;
}
