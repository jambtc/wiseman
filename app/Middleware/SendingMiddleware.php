<?php

namespace App\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Sending;


class SendingMiddleware implements Sending
{
    /**
    * Handle an outgoing message payload before/after it hits the
    * message service.
    *
    * @param mixed $payload
    * @param callable $next
    * @param BotMan $bot
    *
    * @return mixed
    */
    public function sending($payload, $next, BotMan $bot) {
        // $message->addExtras('timestamp', time());
        return $next($payload);
    }

}
