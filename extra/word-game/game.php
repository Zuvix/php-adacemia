<?php

// Put any words you want to include in your game here
define('WORDS', array('elephant', 'squirrel', 'antilope', 'tiger', 'salamander'));

class WordGenerator
{
    public $allowed_characters = 'abcdefghijklmnopqrstuvwxyz';
    public static function generate_random_string($length, $allowed_characters)
    {
        $word = '';
        for ($i = 0; $i < $length; $i++) {
            $word .= $allowed_characters[rand(0, strlen($allowed_characters) - 1)];
        }
        return $word;
    }

    public static function get_random_word()
    {
        return WORDS[rand(0, count(WORDS) - 1)];
    }

    public static function find_and_replace_character_in_blank_string(&$blank_word, $word_to_guess, $character)
    {
        for ($i = 0; $i < strlen($blank_word); $i++) {
            if ($word_to_guess[$i] == $character) $blank_word[$i] = $character;
        }
    }
    public static function create_guessing_string($progress, $word_to_guess)
    {
        $blank_word = str_repeat('*', strlen($word_to_guess));

        foreach ($progress as $character) {
            if (str_contains($word_to_guess, $character)) {
                WordGenerator::find_and_replace_character_in_blank_string($blank_word, $word_to_guess, $character);
            }
        }
        return $blank_word;
    }
}
class HelperFunctions
{
    public static function add_letter_to_array($new_letter, $my_array)
    {
        if (!in_array($new_letter, $my_array)) {
            array_push($my_array, $new_letter);
        }
        return $my_array;
    }
}

class SessionManager
{
    public static function resolve_post_form($progress, $word_to_guess)
    {
        if (isset($_POST['word'])) {
            $sanitized_input = preg_replace("/[^a-zA-Z]+/", "", $_POST['word'],);
            $sanitized_input = strtolower($sanitized_input);
            if (strlen($sanitized_input) == 1) {
                $progress = HelperFunctions::add_letter_to_array($sanitized_input, $progress);
            } else {
                if ($sanitized_input == $word_to_guess) {
                    foreach (str_split($word_to_guess) as $letter) {
                        $progress = HelperFunctions::add_letter_to_array($letter, $progress);
                    }
                }
            }
        }
        $_SESSION['progress'] = $progress;
        return $progress;
    }

    public static function load_word()
    {
        if (isset($_SESSION['word_to_guess'])) {
            return $_SESSION['word_to_guess'];
        } else {
            $word = WordGenerator::get_random_word();
            $_SESSION['word_to_guess'] = $word;
            return $word;
        }
    }
    public static function load_word_progres()
    {
        if (isset($_SESSION['progress'])) {
            return $_SESSION['progress'];
        } else {
            $progress = array();
            $_SESSION['progress'] = $progress;
            return $progress;
        }
    }
}
class GameManager
{
    public $moves = 10;
    public $guessing_word = "";

    public function __construct($guessing_word)
    {
        $this->guessing_word = $guessing_word;
    }

    function calculate_remaining_moves()
    {
        if (isset($_SESSION['moves'])) {
            $this->moves = $_SESSION['moves'] - 1;
        }
        $_SESSION['moves'] = $this->moves;
    }

    public function resolve_game_state()
    {
        if (!str_contains($this->guessing_word, '*')) {
            session_destroy();
            return 'Congratulations, you win!';
        }
        $this->calculate_remaining_moves();
        if ($this->moves <= 0) {
            session_destroy();
            return 'Game over, better luck next time!';
        }
        return false;
    }
}


session_start();
$word_to_guess = SessionManager::load_word();
$previous_progress = SessionManager::load_word_progres();
$progress = SessionManager::resolve_post_form($previous_progress, $word_to_guess);
$guessing_word = WordGenerator::create_guessing_string($progress, $word_to_guess);
$game_manager = new GameManager($guessing_word);
$game_over = $game_manager->resolve_game_state();


?>
<form method="post" action="game.php" style="text-align:center;">
    <div class="intro">
        <h1 style="text-align:center;">Animal guessing quiz!</h1>
        <p style="text-align:center;">Uncover the hidden word within 10 attempts.<br>
            Enter any letter to reveal if it is present in the word.<br>
            If you're confident in your skills, go ahead and take a guess at the entire word. <br>Best of luck!</p>
        <p><b>Hidden word:</b></p>
        <p><?php echo $guessing_word ?></p>
        <?php if ($game_over == false) : ?>
            <p><b>Turns left:</b></p>
            <p><b><?php echo $game_manager->moves ?></b></p>
            <p><b>Please write new a letter or a word!</b></p>


            <div class="guessing">
                <label for="word">Type here: </label>
                <input type="text" id="word" name="word" required />
            </div>
            <button type="submit" style="margin-top:5px;">Submit the guess</button>
        <?php else : ?>
            <p><b><?php echo $game_over ?></b></p>
            <button type="submit" style="margin-top:5px;">Restart</button>
        <?php endif; ?>
    </div>
</form>