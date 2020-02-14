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

        //Random move for current player
        if (isset($_POST['randommove'])) {            
            $moves = $_POST['randommove'];
            $turn = $moves['turn'];
            
            $gp = $game->get_board()->get_gridpositions();

            //Which squares do contain a piece
            //that has the color of current player?
            $inclusions = array();
            for($y=0;$y<8;$y++) {
                for($x=0;$x<8;$x++) {
                    if (isset($gp[$x][$y])) {
                        $piece = $gp[$x][$y];
                        //Make sure only select pieces of current color
                        if ($piece !== null && $piece->get_color() != $turn) {
                            $inclusions[] = array($x,$y);
                        }
                    }
                }
            }


            //Randomize item from array inclusions
            //(because this array contains x,y-values with a piece and this turns color)
            //and fetch start position (x,y)

            tryagain:

            $already_included_key = array();
            $found_valid = false;
            while ($found_valid === false) {
                
                $use_key = false;
                while ($use_key === false) {
                    $inclusions_key = array_rand($inclusions, 1); //1 = return only one index from array

                    if (in_array($inclusions_key, $already_included_key, true) !== true) {
                        $use_key = true;
                        $already_included_key[] = $inclusions_key;
                        break;
                    }
                }

                //Is number of pieces done?
                //If so then this is checkmate
                if (count($already_included_key) == count($inclusions)) {
                    $result = array();
                    $result['board'] = $game_obj->draw();
                    $result['turn'] = $turn; 
                    $result['status'] = "CHECK MATE!!!";
                    $result['moved'] = array($x1, $y1, $x2, $y2);
                    echo json_encode($result);
                    return;        
                }

                $use_arritem = $inclusions[$inclusions_key];
                $x1 = $use_arritem[0];
                $y1 = $use_arritem[1];

                //Random move (x,y) to (random from valid moves of selected piece)
                $selected_piece = $gp[$x1][$y1];
                $valid_moves = $selected_piece->get_validmoves($gp, $x1, $y1, $x1, $y1);
                if (!empty($valid_moves)) {                    
                    $valid_key = array_rand($valid_moves, 1);
                    if ($valid_key != null) {
                        $use_validitem = $valid_moves[$valid_key];                        
                        if (isset($use_validitem[0]) && isset($use_validitem[1])) {
                            $x2 = $use_validitem[0];
                            $y2 = $use_validitem[1];    
                            $found_valid = true;
                        }
                    }
                    
                }

            }


            $game_obj = $game->move_to($x1,$y1,$x2,$y2,$turn);

            $status = $game_obj->get_status();
            if ($status == 'redo') {
                goto tryagain;
            }

            $_SESSION['game'] = serialize($game_obj);

            if ($turn == 0) {
                $turn = 1;
            }
            else {
                $turn = 0;
            }
            $result = array();
            $result['board'] = $game_obj->draw();
            $result['turn'] = $game_obj->get_whosturn(); 
            $result['status'] = $status;
            $result['moved'] = array($x1, $y1, $x2, $y2);
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