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
 * Interface ShortcodeValidationInterface.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
interface ShortcodeValidationInterface
{
    /**
     * @param Request $request
     *
     * @return bool
     */
    public function validate(Request $request): bool;

    /**
     * @param string $uri
     *
     * @return bool
     */
    public function isUriExcluded(string $uri): bool;
}
