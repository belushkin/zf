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
    private $stopName       = '';
    private $editMessageId;

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

    public function setEditMessageId($id)
    {
        $this->editMessageId = $id;
        return $this;
    }

    public function getEditMessageId()
    {
        return $this->editMessageId;
    }

    public function text($id)
    {
        //sleep(1);
        $routes     = $this->callStopInfo($id);

        // Sort the routes
        $key    = array_column($routes, 'transportKey');
        $title  = array_column($routes, 'title');

        // Sort the data with volume descending, edition ascending
        // Add $data as the last parameter, to sort by the common key
        array_multisort($key, SORT_ASC, $title, SORT_ASC, $routes);

        $cache      = [];
        $text       = [];

        $text[] = '*'.$this->stopName.':*';

        $separator = '';
        foreach ($routes as $route) {
            if (in_array($route->id, $cache)) { // removing duplicates from Eway API
                continue;
            }
            if ($route->transportKey != $separator) {
                $text[] = '';
                $separator = $route->transportKey;
            }

            $string = '*'.$route->transportName.'*' . ' №' . $route->title . ', ';
            //$string .= 'в напрямку: ' . $route->directionTitle . ', ';
            $string .= 'прибуде через ' . $route->timeLeftFormatted;

            $text[] = $string;
            //$text[] = '';
            $cache[] = $route->id;
        }

        $button = new InlineKeyboardButton(['text' => 'Оновити', 'callback_data' => 1 . '_' . $id]);
        $keyboard = new InlineKeyboard($button);

        $keyboard->setResizeKeyboard(true);

        $data['chat_id']        = $this->getMessage()->getChat()->getId();
        $data['text']           = implode(PHP_EOL, $text);
        $data['parse_mode']     = 'Markdown';
        $data['reply_markup']   = $keyboard;

        if ($this->getEditMessageId()) {
            $data['message_id'] = $this->getEditMessageId();
            $data['text'] = implode(PHP_EOL, $text);
            return Request::editMessageText($data);
        }
        return Request::sendMessage($data);
    }

    private function callStopInfo($id)
    {
        $body = $this->app['app.eway']->handleStopInfo($id);
        $this->stopName = (isset($body->title)) ? $body->title : "";

        if (isset($body->routes) && is_array($body->routes) && !empty($body->routes)) {
            return $body->routes;
        }
        return [];
    }

}
