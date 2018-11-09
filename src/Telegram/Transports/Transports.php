<?php

namespace Bus115\Telegram\Transports;

use Silex\Application;
use Longman\TelegramBot\Entities\InlineKeyboardButton;
use Longman\TelegramBot\Entities\InlineKeyboard;
use Longman\TelegramBot\Request;

class Transports
{

    private $app;
    private $message;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function setMessage($message)
    {
        $this->message = $message;
        return $this;
    }

    public function getMessage()
    {
        return $this->message;
    }

    public function text($id)
    {
        $routes     = $this->callStopInfo($id);
        $elements   = [];
        $cache      = [];

        foreach ($routes as $route) {
            if (in_array($route->id, $cache)) { // removing duplicates from Eway API
                continue;
            }
            $button = new InlineKeyboardButton(['text' => 'Оновити час прибуття', 'callback_data' => $id . '_'. $route->id]);
            $keyboard = new InlineKeyboard($button);
            $keyboard->setResizeKeyboard(true);
            $elements[] = [
                'chat_id'       =>  $this->getMessage()->getChat()->getId(),
                'caption'       =>  $route->transportName . ' №' . $route->title,
                'photo'         => "https://bus115.kiev.ua/images/{$route->transportKey}.jpg",
                'reply_markup'  =>  $keyboard

//                'title'     => $route->transportName . ' №' . $route->title,
//                'subtitle'  => 'В напрямку:' . $route->directionTitle,
//                'image_url' => "https://bus115.kiev.ua/images/{$route->transportKey}.jpg",
//                'buttons' => [
//                    [
//                        'type' => 'postback',
//                        'title' => 'Оновити час прибуття',
//                        'payload' => $id . '_'. $route->id
//                    ]
            ];
            $cache[] = $route->id;
        }
        if (empty($elements)) {
            $data = [
                'chat_id' => $this->getMessage()->getChat()->getId(),
                'text'    => 'Маршрути не знайдено, для допомоги надрукуй /help',
            ];
            return Request::sendMessage($data);
        }
        return $this->app['app.telegram.response']->photos($elements);
    }

    private function callStopInfo($id)
    {
        $body = $this->app['app.eway']->handleStopInfo($id);
        if (isset($body->routes) && is_array($body->routes) && !empty($body->routes)) {
            return $body->routes;
        }
        return [];
    }

}
