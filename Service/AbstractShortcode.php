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
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class AbstractShortcode.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
abstract class AbstractShortcode implements ShortcodeInterface
{
    /**
     * @var array
     */
    protected $options;

    /**
     * @param OptionsResolver $resolver
     */
    abstract public function setDefaultOptions(OptionsResolver $resolver): void;

    /**
     * @inheritDoc
     */
    abstract public function getTag(): string;

    /**
     * @inheritDoc
     */
    abstract public function output(): string;

    /**
     * @param Request|null $request
     *
     * @return bool
     */
    public function isInvoked(Request $request = null): bool
    {
       return true;
    }

    /**
     * @param array $options
     *
     * @return void
     */
    public function configureOptions(array $options): void
    {
        $resolver = new OptionsResolver();

        $this->setDefaultOptions($resolver);

        $this->options = $resolver->resolve($options);
    }
}
