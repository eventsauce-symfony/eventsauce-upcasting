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

By default, upcasting works before deserializing the message.
This library allows upcasting on the message object after deserialization.

#### Usage

```php
interface MessageUpcaster
{
    public function upcast(Message $message): Message;
}
```

For example

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

You can use multiple upcasters using the **MessageUpcasterChain**.

```php

use Andreo\EventSauce\Upcasting\MessageUpcasterChain;

new MessageUpcasterChain(
    new SomeUpcaster(),
    new AnotherUpcaster(),
)
    
```

#### Use upcaster as message serializer


```php

use Andreo\EventSauce\Upcasting\UpcastingMessageObjectSerializer;

new UpcastingMessageObjectSerializer(
    messageSerializer: $messageSerializer, // default EventSauce\EventSourcing\Serialization\MessageSerializer
    upcaster: new MessageUpcasterChain(new SomeUpcaster())
)
    
```

then use it in **MessageRepository**

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
    #[Event(event: SomeEvent::class)]
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

### Applying event problem

By default, EventSauce aggregate events based on method name
[convention](https://eventsauce.io/docs/event-sourcing/create-an-aggregate-root/)
apply{EventClassName}.
The recommended way of event upcasting is to return an event of a new type

For example

```php
use Andreo\EventSauce\Upcasting\MessageUpcaster;
use EventSauce\EventSourcing\Message;
use Andreo\EventSauce\Upcasting\Event;

final class SomeUpcaster implements MessageUpcaster
{
    #[Event(event: SomeEvent::class)]
    public function upcast(Message $message): Message
    {
        $event = $message->event();
        assert($event instanceof SomeEvent);

        return new Message(new SomeEventV2()); // new event type v2
    }
}
```

So you need to rename the method in the aggregate

```php
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class SomeAggregate implements AggregateRoot
{
    use AggregateRootBehaviour;
    
    // before applySomeEvent
    public function applySomeEventV2(ProcessWasInitiated $event): void
    {
    }
}
```
It is not comfortable. To avoid this, 
I recommend using this [extension](https://github.com/andrew-pakula/eventsauce-aggregate)

