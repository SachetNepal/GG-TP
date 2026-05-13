<?php

declare(strict_types=1);

/**
 * Static trader UI under public/trader-portal (PHP templates, no DB).
 */
function tp_h(?string $s): string
{
    return htmlspecialchars((string) $s, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}
