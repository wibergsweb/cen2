<?php
session_start(); //Store game-object in session (serialize / unserialize when needed)
require_once("chessengine.php");
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

        //Random move for current
        if (isset($_POST['randommove'])) {            
            $moves = $_POST['randommove'];
            $turn = $moves['turn'];      
            if ($turn == 0) {
                $result = array();
                $result['board'] = $game->draw();      
                $result['turn'] = $game->get_whosturn();     
                $result['status'] = $game->get_status();
                echo json_encode($result);
                return;
            }      
            $gp = $game->get_board()->get_gridpositions();
            $ce = new Chessengine($game, $gp, $turn);
            $result = $ce->get_randommove();
            echo json_encode($result);
            return;
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
            return;
        }
        else {
            $result = array();
            $result['board'] = $game->draw();      
            $result['turn'] = $game->get_whosturn();     
            $result['status'] = $game->get_status();
            echo json_encode($result);
            return;
        }        
    }
}
$cm = new Chessmover;
?>