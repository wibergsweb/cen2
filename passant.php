<?php
class Passant extends Piece {    

    public function get_validmoves($gridpositions, $x,$y,$x2,$y2,$check_other_players_color=true) { 
        return array();
    }

    public function get_aftermove($gridpositions, $x,$y) {
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
