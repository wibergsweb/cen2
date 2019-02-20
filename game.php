<?php
require_once('piece.php');
require_once('pawn.php');
require_once('rook.php');
require_once('knight.php');
require_once('bishop.php');
require_once('queen.php');
require_once('king.php');
require_once('passant.php');
require_once('board.php');

class Game {
    private $boardobj;
    private $whos_turn;
    private $gridpos;
       
    /*
     * $forward = -1 means white at bottom of board, 1 means white at top of board
     */
    public function __construct($forward = -1) {
        $backward = -abs($forward);
        $this->boardobj = new board();
        $this->boardobj->game_start();
    }
    
    
    public function move_to($x1,$y1,$x2,$y2) {
        $make_move = false;
        echo 'x1=' . $x1 . ', y1=' . $y1;
         echo 'TO x2=' . $x2 . ', y2=' . $y2;
        $this->gridpos = $this->boardobj->get_gridpositions();
        $active_piece = $this->boardobj->get_piece($x1,$y1);   
        $valid_moves = $active_piece->get_validmoves($this->gridpos,$x1,$y1,$x2,$y2);                
        echo '<pre>';
        var_dump ($valid_moves);
        var_dump($active_piece);
        echo '</pre>';

        
        //Make sure player only are able to go to valid locations
        foreach($valid_moves as $vm) {
            $check_movetox = $vm[0];
            $check_movetoy = $vm[1];
            if (($check_movetox == $x2) && ($check_movetoy == $y2)) {
                $make_move = true;  
                break;
            }
        }
        
        if ($make_move == false) {
            echo '<h2>Invalid move. Nothing happens on board!</h2>';
            $this->draw();
            return;
        }        

        $this->gridpos[$x1][$y1] = null;
        $this->gridpos[$x2][$y2] = $active_piece;
        
        $after_move = $active_piece->get_aftermove($this->gridpos,$x2,$y2);
        echo '<b>' .$after_move[1] .'</b>';    
        
        //Regenerate gridpos (after move)
        $this->gridpos = array_slice($after_move[0],0,count($after_move[0]));
        
        if ($active_piece->get_waituser() === false) {
            $active_piece->last_move($x2,$y2);
            $active_piece->not_first_move();
            $this->boardobj->renew($this->gridpos);
            $this->draw();
        }        
        
    }
    
    public function player_has_chosenpiece($piece,$x,$y) {        
        $piece->last_move($x2,$y2);
        $this->gridpos[$x][$y] = $piece;
        $this->boardobj->renew($this->gridpos);
        $this->draw();    
        echo 'User has selected piece now.';
    }
    
    public function draw() {
        echo $this->boardobj->output_html();
    }
    
    
}
$game = new Game();

$game->move_to(5,6,5,4); //white
$game->move_to(6,1,6,3); //black


$game->move_to(5,4,5,3); //white
$game->move_to(4,1,4,3); //black



$game->move_to(5,3,4,2); //white
$game->move_to(5,1,4,2); //black

$game->move_to(4,6,4,5); //white
$game->move_to(4,2,4,3); //black

$game->move_to(3,7,7,3); //white
$game->move_to(4,0,4,1); //black

$game->move_to(5,7,2,4); //white
$game->move_to(0,1,0,2); //black

$game->move_to(7,3,5,1); //white
$game->move_to(4,1,3,2); //black

$game->move_to(0,6,0,5); //white
$game->move_to(6,0,5,2); //black

$game->move_to(1,6,1,4); //white
$game->move_to(7,1,7,2); //black

$game->move_to(5,1,4,2); //white
$game->move_to(3,2,4,2); //black

/*
*/

/*
$game->move_to(4,7,5,6); //white
$game->move_to(5,6,5,5); //white
$game->move_to(5,5,4,4); //white

$game->move_to(4,4,5,4); //white

$game->move_to(5,4,6,3); //white

$game->move_to(7,6,7,4); //white

$game->move_to(7,7,7,5); //white
$game->move_to(7,5,0,5); //white
$game->move_to(0,5,0,1); //white
$game->move_to(0,1,1,1); //white



$game->move_to(0,0,0,6); //black


$game->move_to(3,7,4,7); //white
$game->move_to(4,7,6,5); //white

$game->move_to(5,0,7,2); //black
$game->move_to(6,0,4,1); //black
$game->move_to(7,2,6,1); //black
$game->move_to(6,1,7,2); //black
$game->move_to(4,1,3,3); //black
$game->move_to(6,3,6,2); //white
$game->move_to(3,3,5,4); //black
$game->move_to(3,0,6,3); //black
$game->move_to(7,0,6,0); //black
$game->move_to(6,2,5,2); //white

$game->move_to(4,0,4,1); //black
*/