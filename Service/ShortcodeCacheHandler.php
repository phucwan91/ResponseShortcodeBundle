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

use Symfony\Component\Cache\Adapter\AdapterInterface;

/**
 * Class ShortcodeCacheHandler.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeCacheHandler implements ShortcodeCacheHandlerInterface
{
    /**
     * @var AdapterInterface
     */
    private $adapter;

    /**
     * ShortcodeCacheHandler constructor.
     *
     * @param AdapterInterface $adapter
     */
    public function __construct(AdapterInterface $adapter)
    {
        $this->adapter = $adapter;
    }

    /**
     * @inheritDoc
     */
    public function retrieveOutput(string $key, string $output): string
    {
        $item = $this->adapter->getItem(md5(sprintf('shortcode_%s', $output)));

        if (!$item->isHit()) {
            $item->set($output);
            $this->adapter->save($item);
        }

        return $item->get();
    }
}
