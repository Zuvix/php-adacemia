<?php

//Import the used classes
require_once('JsonLogger.php');
require_once('StudentJsonLogger.php');
require_once('ArrivalJsonLogger.php');

function get_current_time()
{
    $now = new DateTime();
    return $now->format('Y-m-d H:i:s');
}

function sanitize_input($input)
{
    return preg_replace("/[^a-zA-Z]+/", "", $input);
}

//Core function to process input from form or url
function process_post_or_get($arrival_logger)
{
    ///Guard condition
    if (!isset($_POST['meno']) && !isset($_GET['meno'])) return;

    if (isset($_POST['meno'])) {
        $sanitized_input = sanitize_input($_POST['meno']);
    } else {
        $sanitized_input = sanitize_input($_GET['meno']);
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
print_r(StudentJsonLogger::load_json(StudentJsonLogger::STUDENTS_FILE_NAME));
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