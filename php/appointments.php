<?php

class appointments {

private $user;
private $date;
private $time;
private $appointment = [];

 public function __construct($user, $date, $time) {
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
    }
}