## eventsauce-upcasting 3.0

Extended upcasting components for EventSauce

[About Upcasting](https://eventsauce.io/docs/advanced/upcasting/#main-article)

### Installation

```bash
composer require andreo/eventsauce-upcasting
```

#### Previous versions doc

- [2.0](https://github.com/eventsauce-symfony/eventsauce-upcasting/tree/2.0.0)

### Requirements

- PHP >=8.2

### Message upcaster

```php

use Andreo\EventSauce\Upcasting\MessageUpcaster\MessageUpcaster;
use EventSauce\EventSourcing\Message;

final class FooUpcaster implements MessageUpcaster
{
    public function upcast(Message $message): Message
    {
        $event = $message->payload();
        if (!$event instanceof FooEvent)) { 
            return $message;
        }

        return new Message(new FooEventV2()); 
    }
}
```

#### Multiple message upcasters

```php

use Andreo\EventSauce\Upcasting\MessageUpcasterChain;

new MessageUpcasterChain(
    new SomeUpcaster(),
    new AnotherUpcaster(),
)
    
```

#### Upcaster as MessageSerializer

`For use in MessageRepository`

```php

use Andreo\EventSauce\Upcasting\UpcastingMessageObjectSerializer;

new UpcastingMessageObjectSerializer(
    messageSerializer: $messageSerializer, // default EventSauce\EventSourcing\Serialization\MessageSerializer
    upcaster: new MessageUpcasterChain(new SomeUpcaster())
)
    
```

### Event guessing

```php
use EventSauce\EventSourcing\Message;
use Andreo\EventSauce\Upcasting\MessageUpcaster\MessageUpcaster;
use Andreo\EventSauce\Upcasting\MessageUpcaster\Event;

final class FooUpcaster implements MessageUpcaster
{
    #[Event(event: FooEvent::class)]
    public function upcast(Message $message): Message
    {
        $event = $message->payload();
        assert($event instanceof FooEvent);

        return new Message(new FooEventV2()); 
    }
}
```

### Handling events of aggregate

By default, EventSauce applies the events based on method name
[convention](https://eventsauce.io/docs/event-sourcing/create-an-aggregate-root/)
apply{EventClassName}.

So you need to rename the method in the aggregate

```php
use EventSauce\EventSourcing\AggregateRoot;
use EventSauce\EventSourcing\AggregateRootBehaviour;

final class FooAggregate implements AggregateRoot
{
    use AggregateRootBehaviour;
    
    // before applyFooEvent
    public function applyFooEventV2(FooEventV2 $event): void
    {
    }
}
```

You can skip this by using [component](https://github.com/andrew-pakula/eventsauce-aggregate)

