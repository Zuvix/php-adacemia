<?php

function get_current_time()
{
    $now = new DateTime();
    return $now->format('Y-m-d H:i:s');
}
class JsonLogger
{
    protected static function save_json($file, $content)
    {
        $handle = fopen($file, 'w');
        fwrite($handle, $content);
        fclose($handle);
    }
    protected static function load_json($file)
    {
        if (file_exists($file)) {
            $str_json_file_contents = file_get_contents($file);
            return $str_json_file_contents;
        } else {
            $empty_json = array();
            return json_encode($empty_json);
        }
    }
    public static function json_to_array($json)
    {
        $decoded_contents = json_decode($json, true);
        if ($decoded_contents) {
            return $decoded_contents;
        } else {
            return array();
        }
    }
}

class StudentJsonLogger extends JsonLogger
{
    public static $file = "studenti.json";

    //extend parent method
    public static function load_json($file)
    {
        $json = parent::load_json($file);
        if (empty($json)) {
            $json = [
                'count' => 0,
                'students' => []
            ];
            $json = json_encode($json);
        }
        return $json;
    }

    //static method to match the task in the assignment
    public static function log_student_to_json($student_name)
    {
        $json = StudentJsonLogger::load_json(StudentJsonLogger::$file);
        $decoded_json = StudentJsonLogger::json_to_array($json);
        array_push($decoded_json['students'], $student_name);
        $decoded_json['count']++;
        $json_students = json_encode($decoded_json);
        StudentJsonLogger::save_json(StudentJsonLogger::$file, $json_students);
    }
}

class ArrivalJsonLogger extends JsonLogger
{
    public $file;
    public $arrivals;
    public function __construct($file)
    {
        $this->file = $file;
        $json_arrivals = parent::load_json($file);
        $this->arrivals = parent::json_to_array($json_arrivals);
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

function sanitize_input($input)
{
    return preg_replace("/[^a-zA-Z]+/", "", $input);
}

//Core function to process input from form or url
function process_post_or_get($arrival_logger)
{
    if (isset($_POST['meno'])) {
        $sanitized_input = sanitize_input($_POST['meno']);
    } else if (isset($_GET['meno'])) {
        $sanitized_input = sanitize_input($_GET['meno']);
    } else {
        return;
    }
    //shortest name has atleast 3 letters
    if (strlen($sanitized_input) >= 3) {
        $arrival_logger->log_arrival_to_json();
        StudentJsonLogger::log_student_to_json($sanitized_input);
    } else {
        die("Invalid name set!");
    }
}


//Run code
$arrival_logger = new ArrivalJsonLogger('prichody.json');
process_post_or_get($arrival_logger);

echo '<b>Študenti v triede:<br></b>';
print_r(StudentJsonLogger::json_to_array(StudentJsonLogger::load_json(StudentJsonLogger::$file)));
echo '<br>';

echo '<br><b>Časy prichodov:<br></b>';
print_r($arrival_logger->arrivals);
echo '<br>';


$arrival_logger->show_late_arrivals();
echo '<br>';

?>

<form action="index.php" method="post">
    <label for="meno">Meno študenta:</label><br>
    <input type="text" id="meno" name="meno"><br>
    <input type="submit" value="Submit">
</form>