<?php
abstract class Piece {
    public $first_move = true;
    public $color; //0 = black, 1 = white
    public $last_move;

    public function get_color() {
        return $this->color;
    }
    
    
    public function last_move($x,$y) {
        $this->last_move = array($x,$y);
    }
    
    public function not_first_move() {        
        $this->first_move = false;
    }
    
    public function get_waituser() {
        return false;
    }
    
    //This validation is done in each piece-class (pawn, knight, bishop etc)
    abstract public function get_validmoves($gridpositions, $x,$y);
    
    //Get chess character
    abstract protected function get_char();
    
}
