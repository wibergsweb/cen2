<?php
class Passant extends Piece {    
    
    public function __construct($color) {
            $this->color = $color;          
    }
    
    
    public function get_validmoves($gridpositions, $x,$y) {   
        return array();
    }
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '<span style="color:#000;">P</span>';
        }
        return '<span style="color:#fff;">P</span>';
    } 
}
