<?php
session_start(); //Store game-object in session (serialize / unserialize when needed)
class Chessmover {
    public function __construct() {
        require_once 'game.php';

        if (isset ($_POST['startover'])) {
            if ($_POST['startover']['reset'] == 1) {
                unset($_SESSION['game']);
            }
        }
        if (isset($_SESSION['game'])) {
            $game = unserialize($_SESSION['game']);
        }
        else {
            $game = new Game();
        }
        
        //These posted values comes from chess.js
        //(When a user clicks on a square on the board)
        //
        if ( isset($_POST['movements'])) {  
            $moves = $_POST['movements'];
            $x1 = $moves['x1'];
            $y1 = $moves['y1'];
            $x2 = $moves['x2'];
            $y2 = $moves['y2'];
            $result = $game->move_to($x1,$y1,$x2,$y2);
            echo $result['html'];
            $_SESSION['game'] = serialize($game);
        }
        else {
            $result = $game->draw();
            echo $result;
        }        
    }
}
$cm = new Chessmover;
?>