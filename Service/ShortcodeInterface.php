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

use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShortcodeInterface.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
interface ShortcodeInterface
{
    const CONCATENATION_CHAR = '+';

    /**
     * Provide a tag name for short code
     *
     * @return string
     */
    public function getTag(): string;

    /**
     * @return string
     */
    public function output(): string;

    /**
     * @param Request|null $request
     *
     * @return bool
     */
    public function isInvoked(Request $request = null): bool;
}
