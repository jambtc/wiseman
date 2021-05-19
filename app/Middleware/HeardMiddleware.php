<?php

namespace App\Middleware;

use BotMan\BotMan\BotMan;
use BotMan\BotMan\Interfaces\Middleware\Heard;
use BotMan\BotMan\Messages\Incoming\IncomingMessage;


class HeardMiddleware implements Heard
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
    public function heard(IncomingMessage $message, $next, BotMan $bot) {
        // $message->addExtras('timestamp', time());
        return $next($message);
    }

}
