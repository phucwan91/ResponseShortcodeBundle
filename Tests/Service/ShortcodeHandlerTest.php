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

use Ekino\ResponseShortcodeBundle\Service\ShortcodeHandler;
use PHPUnit\Framework\TestCase;

/**
 * Class ShortcodeHandlerTest.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeHandlerTest extends TestCase
{
    /**
     * @return void
     */
    public function testGetShortcodesFromContent(): void
    {
        $handler = new ShortcodeHandler('[[+]]');
        $content = '<html><body>[[ foo {"id": 1} ]] Lorem ipsum dolor sit amet, {{ invalid }} [[bar]]consectetur adipiscing elit, [[foobar {id: 1}} ]]sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam</body></html>';

        $this->assertSame($handler->getShortcodeTagsFromContent($content), [
            ' foo {"id": 1} ', // have to keep space to replace back to content
            'bar',
            'foobar {id: 1}} '
        ]);
    }

    /**
     * @dataProvider tagProvider
     *
     * @param string $tag
     * @param array  $expected
     */
    public function testExtractTag(string $tag, array $expected): void
    {
        $handler = new ShortcodeHandler('[[+]]');

        $this->assertSame($expected, $handler->extractTag($tag));
    }

    /**
     * @return \Generator
     */
    public function tagProvider(): \Generator
    {
        yield ['example {"name": foo}', ['', []]];
        yield ['example {"name": "bar"}}', ['', []]];
        yield ['example {"name": "bar"}{}', ['', []]];
        yield ['example{"id": 1}', ['example', ['id' => 1]]];
        yield ['   example{"id"  : 2  }   ', ['example', ['id' => 2]]];
        yield ['example {"id": 3}', ['example', ['id' => 3]]];
        yield ['example {{"id": 5}{"name": "foo", "age": 18}}', ['', []]];
        yield ['example {"id": 6, "info": {"name": "foo", "age": 18}}', [
            'example',
            [
                'id'   => 6,
                'info' => ['name' => 'foo', 'age' => 18]
            ]
        ]
        ];
    }
}
