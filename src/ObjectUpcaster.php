<?php

declare(strict_types=1);

namespace Andreo\EventSauce\Upcasting;

use EventSauce\EventSourcing\Message;

interface ObjectUpcaster
{
    public function upcast(Message $message): Message;
}
