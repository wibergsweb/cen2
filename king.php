<?php
class King extends Piece {
    //move_pattern (forward=1, backward=0, left=0, right=0)
    //threat() → change move_pattern  (forward=1, backward=0, left=1, right=1)
    //firstmove() → change move_pattern (forward = 1 or 2, backward=0, left=0, right=0)
    //range = 1 //how many steps
    //available_moves(); //based on move_pattern() and range
    
    public function __construct($color) {
            $this->color = $color;
    }
    
    public function get_validmoves($gridpositions, $x,$y) {        
    }
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9818';
        }
        return '&#9812';
    } 
}
