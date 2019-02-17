<?php
class Bishop extends Piece {        
    public $move_steps=1;
    
    public function get_validmoves($gridpositions, $x,$y) {                  
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,-1);  //left up
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,-1);   //right up      
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,-1,1);  //left down
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,1,1);   //right down             
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);        
        return $valid_moves;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,-1,true);  //left up
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,-1,true);   //right up      
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,-1,1,true);  //left down
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,1,1,true);   //right down             
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);   

        $return_str = 'Bishop moved ';
        $chess = $this->check_chess($gridpositions,$valid_moves);
        if ($chess !== false) {
            $return_str .= ':chess (' . $chess[0] . '-' . $chess[1] .')';
        }
        return array($gridpositions,$return_str);
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9821';
        }
        return '&#9815';
    } 
}
