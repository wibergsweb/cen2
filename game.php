<?php
require_once('piece.php');
require_once('pawn.php');
require_once('rook.php');
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
        $this->boardobj->new_game();
        echo $this->boardobj->output_html();
    }
}
$game = new Game();