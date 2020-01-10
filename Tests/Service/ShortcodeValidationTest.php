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

namespace Ekino\ResponseShortcodeBundle\Tests\Service;

use Ekino\ResponseShortcodeBundle\Service\ShortcodeValidation;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

/**
 * Class ShortcodeValidation.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeValidationTest extends TestCase
{
    private const EXCLUDED_URIS = [
        '^/api/(.*)',
        '^/media/cache(.*)',
    ];

    /**
     * @dataProvider requestProvider
     *
     * @param string $method
     * @param string $uri
     * @param bool   $expected
     */
    public function testValidate(string $method, string $uri, bool $expected): void
    {
        $validation = new ShortcodeValidation(static::EXCLUDED_URIS);
        $request    = new Request([], [], [], [], [], ['REQUEST_URI' => $uri]);

        $request->setMethod($method);
        $this->assertSame($expected, $validation->validate($request));
    }

    /**
     * @return \Generator
     */
    public function requestProvider(): \Generator
    {
        yield [Request::METHOD_POST, 'shortcode/v2', false];
        yield [Request::METHOD_GET, 'api/', false];
        yield [Request::METHOD_POST, 'media', false];
        yield [Request::METHOD_GET, 'shortcode/v1', true];
    }
}
