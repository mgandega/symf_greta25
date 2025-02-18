<?php 
namespace App\Events;
use Symfony\Contracts\EventDispatcher\Event;

// FQCN => App\Events\MyEvents
class MyEvents extends Event{

public function __construct(public $firstname, public $lastname){
}


public function presentation(){
    return 'je m\'appelle '.$this->firstname.' '.$this->lastname;
}
}