<?php
class ArrivalJsonLogger extends JsonLogger
{
    public $file;
    public $arrivals;
    public function __construct($file)
    {
        $this->file = $file;
        $this->arrivals = parent::load_json($file);
    }
    public function log_arrival_to_json()
    {
        $current_time = get_current_time();
        array_push($this->arrivals, $current_time);
        $ready_json = json_encode($this->arrivals);
        parent::save_json($this->file, $ready_json);
    }
    public function show_late_arrivals()
    {
        echo '<br><b>Meškania: </b><br>';
        foreach ($this->arrivals as $time_of_arrival) {
            if ($this->check_late_arrival($time_of_arrival)) {
                echo $time_of_arrival . " meskanie!<br>";
            }
        }
    }
    //Private function from assignment
    private function check_late_arrival($time_of_arrival)
    {
        $date = strtotime($time_of_arrival);
        $hour = date('H', $date);


        return $hour >= 8 ? true : false;
    }
}
