<?php
class Passant extends Piece {    

    public function get_validmoves($gridpositions, $x,$y,$x2=null,$y2=null,$check_other_players_color=true) { 
        return array();
    }

    public function get_aftermove($gridpositions, $x,$y) {
        return array();
    }

    //Get chess character (Passant acts like an invisible character on board)
    public function get_char() {
        return '';
    } 
}
