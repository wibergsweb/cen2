<?php
require_once('piece.php');
require_once('pawn.php');
require_once('rook.php');
require_once('knight.php');
require_once('bishop.php');
require_once('queen.php');
require_once('king.php');
require_once('board.php');

class Game {
    private $forward=-1, $backward = 0;
    private $boardobj;
    private $whos_turn;
       
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
        $gridpos = $this->boardobj->get_gridpositions();
        $active_piece = $this->boardobj->get_piece($x1,$y1);
        $valid_moves = $active_piece->get_validmoves($gridpos,$x1,$y1);                
        echo '<pre>';
        var_dump ($valid_moves);
        echo '</pre>';

        
        //Make sure player only are able to go to 
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

        
        $gridpos[$x1][$y1] = null;
        $active_piece->not_first_move();
        $gridpos[$x2][$y2] = $active_piece;
        
        $after_move = $active_piece->get_aftermove($gridpos,$x2,$y2);
        echo '<b>' .$after_move .'</b>';
        
        $this->boardobj->renew($gridpos);
        
        $this->draw();

    }
    
    public function draw() {
        echo $this->boardobj->output_html();
    }
    
    
}
$game = new Game();

$game->move_to(4,6,4,4);
$game->move_to(4,4,4,3);
$game->move_to(4,3,4,2);
$game->move_to(4,2,5,1);
$game->move_to(5,1,6,0);
