<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\Exception;

use EventSauce\EventSourcing\EventSauceException;
use RuntimeException;

final class UpcastFailedException extends RuntimeException implements EventSauceException
{
    public static function unableGuessEventBecauseHeaderNotFound(string $header): self
    {
        return new self(
            sprintf(
                'Unable Guess Event, because header [%s] in event was not found. Check your message decorator definition.',
                $header
            )
        );
    }
}
