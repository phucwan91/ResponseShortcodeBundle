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
 * Class ShortcodeValidation.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeValidation implements ShortcodeValidationInterface
{
    /**
     * @var array
     */
    private $excludedUris;

    /**
     * ShortcodeValidation constructor.
     *
     * @param array $excludedUris
     */
    public function __construct(array $excludedUris)
    {
        $this->excludedUris = $excludedUris;
    }

    /**
     * @inheritDoc
     */
    public function validate(Request $request): bool
    {
        return $request->isMethod(Request::METHOD_GET) &&
            !$this->isUriExcluded($request->getPathInfo())
        ;
    }

    /**
     * @inheritDoc
     */
    public function isUriExcluded(string $uri): bool
    {
        foreach ($this->excludedUris as $uriPattern) {
            if (preg_match(sprintf('#%s#', $uriPattern), $uri)) {
                return true;
            }
        }

        return false;
    }
}
