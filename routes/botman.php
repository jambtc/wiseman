<?php
// use Config;

use App\Http\Controllers\BotManController;
use Botman\BotMan\Messages\Attachments\Audio;
use Botman\BotMan\Messages\Attachments\Video;
use BotMan\BotMan\Messages\Attachments\Image;
use BotMan\BotMan\Messages\Outgoing\OutgoingMessage;

// my conversations
use App\Conversations\OnboardingConversation;
use App\Conversations\ButtonConversation;

use App\Middleware\ReceivedMiddleware;
use App\Middleware\HeardMiddleware;
use App\Middleware\SendingMiddleware;




$botman = resolve('botman');
// $botman->hears('Start conversation', BotManController::class.'@startConversation');

//
$botman->fallback(function ($bot) {
    $message = $bot->getMessage();
    $value = $message->getText();

    if (trim($value) !== ''){
        $timestamp = $bot->getMessage()->getExtras('timestamp');

        $bot->reply($timestamp .' : Non capisco');
        $bot->reply('Prova a digitare "help"');
    }
});
//
$botman->hears('come ti chiami(.*)', function ($bot) {
    $bot->reply('Il mio nome è Wiseman, che significa "Uomo saggio"');
});

$botman->hears('Hi|Hello|Ciao', function ($bot) {
    $bot->reply('Hello!');
});
$botman->hears('Buongiorno', function ($bot) {
    $bot->reply('Buongiorno a lei!');
});

$botman->hears('How are you', function ($bot) {
    $bot->reply('I\'m fine. Thanks!');
});
$botman->hears('Come stai', function ($bot) {
    $bot->reply('Bene, grazie!');
});

$botman->hears('Che tempo fa (.*) {location}', function ($bot, $location) {
    $apikey = Config::get('botman.config.openweather_key');

    // echo '<pre>'.print_r($apikey,true);exit;
    $url = 'https://api.openweathermap.org/data/2.5/weather?q='
        .urlencode($location)
        .'&appid='.$apikey
        .'&lang=it';

    $response = json_decode(file_get_contents($url));
    $array = $response->weather[0];

    // echo '<pre>'.print_r($response->weather[0],true);exit;
    $bot->reply('Il tempo a ' .$response->name. ' è:');
    $bot->reply($array->description);
    $bot->reply('La temperatura è di '. (round($response->main->temp /13,1)).' gradi.');

});

$botman->hears('/gif {name}', function($bot, $name){
    $apikey = Config::get('botman.config.giphy_developer_api');
    $url = 'https://api.giphy.com/v1/gifs/search?api_key='.$apikey
            .'&q='.urlencode($name)
            .'&limit=1&offset=0&rating=g&lang=it';
    $response = json_decode(file_get_contents($url));

    // $data =  $response->data[0];
    $image = $response->data[0]->images->downsized_large->url;

    $message = OutgoingMessage::create('Questa è la tua gif')->withAttachment(
        new Image($image)
    );

    $bot->reply($message);
});

$botman->hears('/video', function($bot){
    $video = 'https://youtu.be/ktKJI9h8dv8';

    $message = OutgoingMessage::create('Questa è il tuo video')->withAttachment(
        new Video($video)
    );

    $bot->reply($message);
});

$botman->hears('il mio nome è {name}', function($bot, $name) {
    $bot->userStorage()->save([
        'name' => $name
    ]);
    $bot->reply('Ciao '.$name);
});
$botman->hears('mi chiamo {name}', function($bot, $name) {
    $bot->userStorage()->save([
        'name' => $name
    ]);
    $bot->reply('Ciao '.$name);
});
$botman->hears('dimmi il mio nome(.*)', function($bot) {
    $name = $bot->userStorage()->get('name');
    $bot->reply('Il tuo nome è '.$name);
});
$botman->hears('come mi chiamo(.*)', function($bot) {
    $name = $bot->userStorage()->get('name');
    $bot->reply('Ti chiami '.$name);
});

// idem con video & audio
$botman->receivesImages(function($bot, $images){
    $image = $images[0];
    $bot->reply(OutgoingMessage::create('Ho ricevuto l\'immagine.')->withAttachment($image));
});

$botman->receivesFiles(function($bot, $files){
    $bot->reply('Ho ricevuto il file.');
});

// conversation class
$botman->hears('survey(.*)', function ($bot) {
    $bot->reply('Va bene, cominciamo.');
    $bot->startConversation(new OnboardingConversation);

});

// conversation class
$botman->hears('conversiamo(.*)', function ($bot) {
    $bot->startConversation(new ButtonConversation);

});


$botman->hears('help|aiuto|guida', function($bot) {
    $guida = [
        // 'come ti chiami',
        // 'Hi',
        // 'hello',
        // 'ciao',
        // 'buongiorno',
        // 'how are you',
        // 'come stai',
        'che tempo fa a {location}',
        '/gif {nome}',
        '/video',
        // 'il mio nome è {nome}',
        // 'dimmi il mio nome',
        // 'mi chiamo {nome}',
        'survey',
        'conversiamo'

    ];
    $bot->reply('Questa è la tua guida. Segui queste istruzioni...');
    $bot->reply('<pre>'.print_r($guida,true).'</pre>');
})->skipsConversation();

$botman->hears('stop|ferma', function($bot) {
    $bot->reply('Ciao. A presto.');
})->stopsConversation();

// heard: in caso di fallback non vengono impostati i parametri Extras
$botman->middleware->heard(new HeardMiddleware());

// con received al contrario si.
$botman->middleware->received(new ReceivedMiddleware());

//
$botman->middleware->sending(new SendingMiddleware());

// middleware class
$botman->hears('received', function ($bot) {
    $timestamp = $bot->getMessage()->getExtras('timestamp');
    $bot->reply( $timestamp . ' : ' .$bot->getMessage()->getText() );
});
