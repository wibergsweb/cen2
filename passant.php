<?php
class Passant extends Piece {    

    public function get_validmoves($gridpositions, $x,$y) {   
        return array();
    }
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '<span style="color:red;">P</span>';
        }
        return '<span style="color:red;">P</span>';
    } 
}
