<?php
/**
 * Description of bishop (löpare)
 *
 * @author Ägaren
 */
class Rook extends Piece {
    //threat() → change move_pattern  (forward=1, backward=0, left=1, right=1)
    //range = 1 //how many steps
    
    public function __construct($color) {
            $this->color = $color;          
    }
    
    public function get_validmoves($gridpositions, $x,$y) {        
    }
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9820';
        }
        return '&#9814';
    }    
}
