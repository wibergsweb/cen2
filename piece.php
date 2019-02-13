<?php
abstract class Piece {
    public $movement_pattern = array();
    public $color; //0 = black, 1 = white

    //This validation is done in each piece-class (pawn, knight, bishop etc)
    abstract public function validate_move();
    
    //Get chess character
    abstract protected function get_char();

}
