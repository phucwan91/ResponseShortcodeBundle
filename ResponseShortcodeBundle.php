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

namespace Ekino\ResponseShortcodeBundle;

use Ekino\ResponseShortcodeBundle\DependencyInjection\Compiler\ShortcodeCompilerPass;
use Ekino\ResponseShortcodeBundle\DependencyInjection\ResponseShortcodeExtension;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class ResponseShortcodeBundle extends Bundle
{
    /**
     * @inheritDoc
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new ShortcodeCompilerPass());
    }

    /**
     * @inheritDoc
     */
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new ResponseShortcodeExtension();
        }

        return $this->extension;
    }
}
