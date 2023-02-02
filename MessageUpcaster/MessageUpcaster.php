<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting\MessageUpcaster;

use EventSauce\EventSourcing\Message;

interface MessageUpcaster
{
    public function upcast(Message $message): Message;
}
