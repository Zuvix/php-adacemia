<?php
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
            $decoded_json = json_decode($str_json_file_contents, true);
            if ($decoded_json != null) {
                return $decoded_json;
            }
        }
        return array();
    }
}
