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
        echo $this->boardobj->output_html();
    }
    
    public function user_response($x,$y) {
        $piece = $this->boardobj->get_piece($x, $y);
        return $piece;
    }
    
}
$game = new Game();
echo '<pre>';
var_dump ($game->user_response(4,6));
echo '</pre>';