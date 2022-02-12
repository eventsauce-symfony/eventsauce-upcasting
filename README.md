## eventsauce-upcasting

Extended upcasting components for EventSauce

[About Upcasting](https://eventsauce.io/docs/advanced/upcasting/#main-article)

### Installation

```bash
composer require andreo/eventsauce-upcasting
```

### Requirements

- PHP ^8.1

### Message upcaster

By default, upcasting works before deserializing a message.
This library allows upcasting on the message object after deserialization.

#### Usage

```php
interface MessageUpcaster
{
    public function upcast(Message $message): Message;
}
```

Example upcaster

```php

use Andreo\EventSauce\Upcasting\MessageUpcaster;
use EventSauce\EventSourcing\Message;

final class SomeUpcaster implements MessageUpcaster
{
    public function upcast(Message $message): Message
    {
        $event = $message->event();
        if (!$event instanceof SomeEvent)) { 
            return $message;
        }

        // do something
    }
}
```

#### Using multiple message upcasters

You can use multiple upcasters using the MessageUpcasterChain.

```php

use Andreo\EventSauce\Upcasting\MessageUpcasterChain;

new MessageUpcasterChain(
    new SomeUpcaster(),
    new AnotherUpcaster(),
)
    
```

#### Use upcaster in message serializer


```php

use Andreo\EventSauce\Upcasting\UpcastingMessageObjectSerializer;

new UpcastingMessageObjectSerializer(
    messageSerializer: $messageSerializer, // default EventSauce\EventSourcing\Serialization\MessageSerializer
    upcaster: new MessageUpcasterChain(new SomeUpcaster())
)
    
```

### Event guessing

Because the message is a wrapping for the event, 
by default you have to manually check an event type. 
You can skip manual event checking thanks to the **Event** attribute

```php

use Andreo\EventSauce\Upcasting\MessageUpcaster;
use EventSauce\EventSourcing\Message;
use Andreo\EventSauce\Upcasting\Event;

final class SomeUpcaster implements MessageUpcaster
{
    #[Event(event: SomeEvent::class)] //guessing the event type
    public function upcast(Message $message): Message
    {
        $event = $message->event();
        assert($event instanceof SomeEvent);

        // do something
    }
}
```

#### Event attribute in default Upcaster

If you want to use event guessing in the default 
implementation of EventSauce, you can do it


Define upcaster 

```php
use EventSauce\EventSourcing\Upcasting\Upcaster;
use Andreo\EventSauce\Upcasting\Event;

final class SomeUpcaster implements Upcaster
{
    #[Event(event: SomeEvent::class)]
    public function upcast(array $message): array
    {

    }
}
```

and use it in dedicated upcaster chain

```php
use Andreo\EventSauce\Upcasting\UpcasterChainWithEventGuessing;

new UpcasterChainWithEventGuessing(
    upcasters: [new SomeUpcaster(),],
);
```


