<?php

namespace Bus115\Messenger\Messages;

use Silex\Application;

class Location implements MessageInterface
{

    private $app;

    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    public function text($term = '')
    {
        $responses[] = [
            'text' => 'Вкажи свою адресу використовуючи мобільний додаток, або просто скажи мені де ти знаходишся',
            'quick_replies' => [
                [
                    'content_type' => 'location',

                ]
            ]
        ];

        return $responses;
    }

}