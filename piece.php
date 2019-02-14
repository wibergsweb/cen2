<?php
abstract class Piece {
    public $first_move = true;
    public $color; //0 = black, 1 = white

    public function get_color() {
        return $this->color;
    }
    
    //This validation is done in each piece-class (pawn, knight, bishop etc)
    abstract public function get_validmoves($gridpositions, $x,$y);
    
    //Get chess character
    abstract protected function get_char();
    
}
