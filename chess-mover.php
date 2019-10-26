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
            $turn = $moves['turn'];
            $game_obj = $game->move_to($x1,$y1,$x2,$y2,$turn);
            $_SESSION['game'] = serialize($game_obj);

            $result = array();
            $result['board'] = $game_obj->draw();
            $result['turn'] = $game_obj->get_whosturn();
            $result['status'] = $game_obj->get_status();
            echo json_encode($result);

        }
        else {
            $result = array();
            $result['board'] = $game->draw();      
            $result['turn'] = $game->get_whosturn();     
            $result['status'] = $game->get_status();
            echo json_encode($result);
        }        
    }
}
$cm = new Chessmover;
?>