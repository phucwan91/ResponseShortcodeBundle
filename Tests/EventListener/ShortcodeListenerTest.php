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

namespace Ekino\ResponseShortcodeBundle\Tests\EventListener;

use Ekino\ResponseShortcodeBundle\DataCollector\ShortcodeCollector;
use Ekino\ResponseShortcodeBundle\EventListener\ShortcodeListener;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeCacheHandlerInterface;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeHandler;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeInterface;
use Ekino\ResponseShortcodeBundle\Service\ShortcodePool;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeValidation;
use Ekino\ResponseShortcodeBundle\Service\ShortcodeValidationInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\FilterResponseEvent;

/**
 * Class ShortcodeListenerTest.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeListenerTest extends TestCase
{
    const IGNORE_URL_PATTERNS = [
        '^/track/user',
        '^/sonata-admin(.*)',
        '^/assetic(.*)',
        '^/api/(.*)',
        '^/front-api(.*)',
        '^/media/cache(.*)',
        '^/styleguide/(.*)',
        '^/uploads/(.*)',
        '^/login(.*)',
        '^/logout(.*)',
        '^/resetting(.*)',
        '^/monitor(.*)',
        '^/sonata/cache/',
        '^/sonata/page/cache/',
        '^/_(?!internal)(.*)',
        '^/webservice(.*)',
        '^/api/doc(.*)',
        '^/dataflow_test/compare_data(.*)',
        '^/sitemap(_.*)?\.xml',
    ];

    /**
     * @var ShortcodePool|MockObject
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
     * @var ShortcodeCacheHandlerInterface|MockObject
     */
    private $cacheHandler;

    /**
     * @var FilterResponseEvent|MockObject
     */
    private $event;

    /**
     * @var ShortcodeCollector|MockObject
     */
    private $collector;

    /**
     * @var ShortcodeInterface|MockObject
     */
    private $shortcode;

    /**
     * @var Request|MockObject
     */
    private $request;

    /**
     * @var ShortcodeListener
     */
    private $listener;

    /**
     * {@inheritdoc}
     */
    protected function setUp(): void
    {
        $this->shortcodePool       = $this->createMock(ShortcodePool::class);
        $this->event               = $this->createMock(FilterResponseEvent::class);
        $this->request             = $this->createMock(Request::class);
        $this->cacheHandler        = $this->createMock(ShortcodeCacheHandlerInterface::class);
        $this->collector           = $this->createMock(ShortcodeCollector::class);
        $this->shortcode           = $this->createPartialMock(ShortcodeInterface::class, ['configureOptions', 'getTag', 'output', 'isInvoked']);
        $this->shortcodeHandler    = new ShortcodeHandler('[[+]]');
        $this->shortcodeValidation = new ShortcodeValidation(static::IGNORE_URL_PATTERNS);
        $this->listener            = new ShortcodeListener(
            '[[+]]',
            $this->shortcodePool,
            $this->shortcodeHandler,
            $this->shortcodeValidation,
            $this->cacheHandler,
            $this->collector
        );
    }

    /**
     * Test with ignore URIs
     *
     * @param string $pattern
     * @param bool   $isSupported
     *
     * @dataProvider ignoreUriProvider
     */
    public function testWithIgnoreUris(string $pattern, bool $isSupported): void
    {
        $this->event
            ->expects($this->once())
            ->method('getRequest')
            ->willReturn($this->request)
        ;
        $this->request
            ->expects($this->once())
            ->method('isMethod')
            ->with(Request::METHOD_GET)
            ->willReturn(true)
        ;
        $this->request
            ->expects($this->once())
            ->method('getPathInfo')
            ->willReturn($pattern)
        ;
        $this->event
            ->expects($this->exactly((int) $isSupported))
            ->method('getResponse')
            ->willReturn(new Response())
        ;
        $this->listener->onKernelResponse($this->event);
    }

    /**
     * @return \Generator
     */
    public function ignoreUriProvider(): \Generator
    {
        yield ['/sonata-admin/lexik/translation/', false];
        yield ['/api/doc', false];
        yield ['/login', false];
        yield ['/monitor', false];
        yield ['/sonata/cache/', false];
        yield ['/_external/_private_contact', false];
        yield ['/_imagine/liip_imagine_filter', false];
        yield ['/_invalid-uri/ignore', false];
        yield ['/_internal/_private_contact', true];
        yield ['/_internal/_payment_account_disclosure', true];
        yield ['/_internal/_alerts_subscribe_nav', true];
    }

    /**
     * Test parse shortcode from content
     */
    public function testParseShortcode(): void
    {
        $param     = new ParameterBag();
        $request   = new Request();
        $response  = new Response();

        $param->set('_controller', 'sonata_controller:list');

        $request->attributes = $param;

        $response->setContent('this is an [[ex_shortcode {"label": "example"}]] in page');

        $this->collector->expects($this->once())->method('get');

        $this->event->expects($this->once())->method('getRequest')->willReturn($request);
        $this->event->expects($this->once())->method('getResponse')->willReturn($response);

        $this->shortcodePool->expects($this->once())->method('getShortcode')->with('ex_shortcode')->willReturn($this->shortcode);

        $this->shortcode->expects($this->once())->method('isInvoked')->willReturn(true);
        $this->shortcode->expects($this->once())->method('configureOptions')->with(['label' => 'example']);
        $this->shortcode->expects($this->once())->method('output')->willReturn('<strong>example short code</strong>');

        $this->cacheHandler->expects($this->once())->method('retrieveOutput')->willReturn('<strong>example short code</strong>');

        $this->listener->onKernelResponse($this->event);

        $this->assertSame('this is an <strong>example short code</strong> in page', $response->getContent());
    }

    /**
     * Test parse shortcode without params
     */
    public function testParseShortcodeWithoutParams(): void
    {
        $param           = new ParameterBag();
        $request         = new Request();
        $response        = new Response();
        $this->shortcode = $this->createPartialMock(ShortcodeInterface::class, ['getTag', 'output', 'isInvoked']);

        $param->set('_controller', 'sonata_controller:list');

        $request->attributes = $param;

        $response->setContent('this is an [[ex_shortcode]] in page');

        $this->collector->expects($this->once())->method('get');

        $this->event->expects($this->once())->method('getRequest')->willReturn($request);
        $this->event->expects($this->once())->method('getResponse')->willReturn($response);

        $this->shortcodePool->expects($this->once())->method('getShortcode')->with('ex_shortcode')->willReturn($this->shortcode);

        $this->shortcode->expects($this->once())->method('isInvoked')->willReturn(true);
        $this->shortcode->expects($this->once())->method('output')->willReturn('<strong>example short code</strong>');

        $this->cacheHandler->expects($this->once())->method('retrieveOutput')->willReturn('<strong>example short code</strong>');

        $this->listener->onKernelResponse($this->event);

        $this->assertFalse(method_exists($this->shortcode, 'configureOptions'));
        $this->assertSame('this is an <strong>example short code</strong> in page', $response->getContent());
    }

    /**
     * Test parse many shortcodes from content
     */
    public function testParseManyShortcodes(): void
    {
        $param    = new ParameterBag();
        $request  = new Request();
        $response = new Response();

        $param->set('_controller', 'sonata_controller:list');

        $request->attributes = $param;

        $response->setContent('this is an [[ex_shortcode {"label": "example"}]] in [[ex_page]]');

        $this->collector->expects($this->exactly(2))->method('get');

        $this->event->expects($this->once())->method('getRequest')->willReturn($request);
        $this->event->expects($this->once())->method('getResponse')->willReturn($response);

        $this->shortcodePool->expects($this->at(0))->method('getShortcode')->with('ex_shortcode')->willReturn($this->shortcode);
        $this->shortcodePool->expects($this->at(1))->method('getShortcode')->with('ex_page')->willReturn($this->shortcode);

        $this->shortcode->expects($this->at(0))->method('isInvoked')->willReturn(true);
        $this->shortcode->expects($this->at(1))->method('configureOptions')->with(['label' => 'example']);
        $this->shortcode->expects($this->at(2))->method('output')->willReturn('<i>example short code</i>');
        $this->shortcode->expects($this->at(3))->method('isInvoked')->willReturn(true);
        $this->shortcode->expects($this->at(4))->method('configureOptions')->with([]);
        $this->shortcode->expects($this->at(5))->method('output')->willReturn('<strong>example page</strong>');

        $this->cacheHandler->expects($this->at(0))->method('retrieveOutput')->willReturn('<i>example short code</i>');
        $this->cacheHandler->expects($this->at(1))->method('retrieveOutput')->willReturn('<strong>example page</strong>');

        $this->listener->onKernelResponse($this->event);

        $this->assertSame(
            'this is an <i>example short code</i> in <strong>example page</strong>',
            $response->getContent()
        );
    }

    /**
     * Test parse with invalid shortcode
     */
    public function testParseWithInvalidShortcode(): void
    {
        $param    = new ParameterBag();
        $request  = new Request();
        $response = new Response();

        $param->set('_controller', 'sonata_controller:list');

        $request->attributes = $param;

        $response->setContent(
            'this is an [[ex_shortcode {"label": "example"} ]] in [[non_existed]] and {{invalid {}{}}}'
        );

        $this->collector->expects($this->exactly(2))->method('get');

        $this->event->expects($this->once())->method('getRequest')->willReturn($request);
        $this->event->expects($this->once())->method('getResponse')->willReturn($response);

        $this->shortcodePool->expects($this->at(0))->method('getShortcode')->with('ex_shortcode')->willReturn($this->shortcode);
        $this->shortcodePool->expects($this->at(1))->method('getShortcode')->with('non_existed')->willReturn(null);

        $this->shortcode->expects($this->once())->method('isInvoked')->willReturn(true);
        $this->shortcode->expects($this->once())->method('configureOptions')->with(['label' => 'example']);

        $this->shortcode->expects($this->once())->method('output')->with()->willReturn('<i>example short code</i>');

        $this->cacheHandler->expects($this->at(0))->method('retrieveOutput')->willReturn('<i>example short code</i>');

        $this->listener->onKernelResponse($this->event);

        $this->assertSame(
            'this is an <i>example short code</i> in [[non_existed]] and {{invalid {}{}}}',
            $response->getContent()
        );
    }
}
