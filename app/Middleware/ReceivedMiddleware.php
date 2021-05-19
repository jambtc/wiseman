<?php

namespace App\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Received;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;


class ReceivedMiddleware implements Received
{
    /**
    * Handle an incoming message.
    *
    * @param IncomingMessage $message
    * @param callable $next
    * @param BotMan $bot
    *
    * @return mixed
    */
    public function received(IncomingMessage $message, $next, BotMan $bot) {
        $message->addExtras('timestamp', time());
        return $next($message);
    }

}
