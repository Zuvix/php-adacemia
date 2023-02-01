<?php
class StudentJsonLogger extends JsonLogger
{
    public const STUDENTS_FILE_NAME = 'studenti.json';

    //extend parent method
    public static function load_json($file)
    {
        $json = parent::load_json($file);
        if (empty($json)) {
            $json = [
                'count' => 0,
                'students' => []
            ];
        }
        return $json;
    }

    //static method to match the task in the assignment
    public static function log_student_to_json($student_name)
    {
        $decoded_json = self::load_json(self::STUDENTS_FILE_NAME);
        array_push($decoded_json['students'], $student_name);
        $decoded_json['count']++;
        $json_students = json_encode($decoded_json);
        self::save_json(self::STUDENTS_FILE_NAME, $json_students);
    }
}
