<?php

class appointmentManager implements bookService
{

private $user;
private $date;
private $time;
private $appointment = [];

 public function __construct($user, $date, $time) {
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
    }

    #[Override]
    public function bookAppointment() {
        $booked = $this->__construct($this->user, $this->date, $this->time);

        $this->appointment = $booked;
    }
    

    #[Override]
    public function message()
    {
        return "Appointment booked successfully!";
        throw new \Exception('Not implemented');
    }

}

?>