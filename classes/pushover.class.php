<?php

/**
 * Created by PhpStorm.
 * User: Adrian
 * Date: 2015-02-22
 * Time: 15:41
 */
class pushover
{
    private $api;

    private $config = array(
        "user-key" => PUSHOVER_USER_KEY,
        "application-key" => PUSHOVER_API_KEY
    );


    public function __construct()
    {
        $this->api = new pushoverAPI();
        $this->api->setToken($this->config["application-key"]);
        $this->api->setUser($this->config["user-key"]);
        $this->api->setDevice("iPhone-Adriana");
        $this->api->setTimestamp(time());

    }

    public function send ($title,$message,$priority=-1) {
        $this->api->setTitle($title);
        $this->api->setMessage($message);
        $this->api->setPriority($priority);
        global $debug;
        $debug->push("Sending PushOver notification");
        $go = $this->api->send();
    }



}
