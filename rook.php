<?php
class Rook extends Piece {
    public $move_steps=1;

    public function get_validmoves($gridpositions, $x,$y) {                
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1);   //down                   
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);        
        return $valid_moves;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0,true);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0,true);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1,true);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1,true);   //down                   
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);

        $return_str = 'Rook moved ';
        $chess = $this->check_chess($gridpositions,$valid_moves);
        if ($chess !== false) {
            $return_str .= ':chess (' . $chess[0] . '-' . $chess[1] .')';
        }       
        return array($gridpositions,$return_str);
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9820';
        }
        return '&#9814';
    }    
}
