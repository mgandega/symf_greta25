<?php

namespace App\EventListener;

use App\Events\MyEvents;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;

final class MyEventListener
{
    // MyEvents::class => App\Events\MyEvents (FQCN)
    #[AsEventListener(event: MyEvents::class)]
    public function onMyEvents(MyEvents $event): void
    {
        dd($event);
    }
}
