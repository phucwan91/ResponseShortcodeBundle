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
 * Class ShortcodeHandler.
 *
 * @author Phuc Vo <van-phuc.vo@ekino.com>
 */
class ShortcodeHandler implements ShortcodeHandlerInterface
{
    /**
     * @var string
     */
    private $formatTag;

    /**
     * ShortcodeHandler constructor.
     *
     * @param string $formatTag
     */
    public function __construct(string $formatTag)
    {
        $this->formatTag = $formatTag;
    }

    /**
     * Get all short code tags from the context
     * Short code tag example: [[tag_name {"name":"John","email":"john@local.fr"}]]
     *
     * @param string $content
     *
     * @return array
     */
    public function getShortcodeTagsFromContent(string $content): array
    {
        $tag = explode(ShortcodeInterface::CONCATENATION_CHAR, $this->formatTag, 2); // add backslashs

        if (\count($tag) !== 2) {
            // TODO output something
            return [];
        }

        $regex = sprintf('/%s(?<tag>.*?)%s/', quotemeta($tag[0]), quotemeta($tag[1]));

        preg_match_all($regex, $content, $matches, PREG_OFFSET_CAPTURE);

        return $matches ? array_column($matches['tag'], 0) : [];
    }

    /**
     * Extract short code tag into tag name and parameters
     *
     * @param string $tag
     *
     * @return array
     */
    public function extractTag(string $tag): array
    {
        // Remove spaces outside quotes
        $tag = preg_replace('/\s+(?=(?:[^\'"]*[\'"][^\'"]*[\'"])*[^\'"]*$)/', '', $tag);
        $tag = explode('{', $tag, 2);

        // Convert string json parameters to array
        $params = json_decode(sprintf('{%s}', substr($tag[1] ?? '', 0, -1)), true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            return ['', []];
        }

        return [$tag[0], $params ?: []];
    }
}
