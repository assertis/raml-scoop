<?php
declare(strict_types=1);

namespace Assertis\RamlScoop\Converters\HTML;

use Jralph\Twig\Markdown\Contracts\MarkdownInterface;
use Michelf\Markdown;

/**
 * @author Michał Tatarynowicz <michal.tatarynowicz@assertis.co.uk>
 */
class MichelfMarkdown implements MarkdownInterface
{
    /**
     * @param string $text
     * @return string
     */
    public function parse($text): string
    {
        static $instance;

        if (empty($instance)) {
            $instance = new Markdown();
        }

        return $instance->transform($text);
    }
}
