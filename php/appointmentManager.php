<?php
require_once "bookService.php";

class appointmentManager implements bookService
{

private $user;
private $date;
private $time;
private $appointments = [];

 public function __construct($user, $date, $time) {
        $this->user = $user;
        $this->date = $date;
        $this->time = $time;
    }

    #[Override]
    public function bookAppointment() {
        try {
            $bookAppointment = $this->__construct($this->user, $this->date, $this->time);

            $this->appointments[] = $bookAppointment;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
        }
    }
    

    #[Override]
    public function message()
    {
        return "Appointment booked successfully!";
        throw new \Exception('Not implemented');
    }

}

?>