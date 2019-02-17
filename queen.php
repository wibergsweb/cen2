<?php
class Queen extends Piece {            
    public $move_steps=1;
    
    public function get_validmoves($gridpositions, $x,$y,$x2,$y2) {              
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1);   //down      
        $valid_moves5 = $this->check_available_squares($gridpositions,$x,$y,-1,-1);  //left up
        $valid_moves6 = $this->check_available_squares($gridpositions,$x,$y,1,-1);   //right up      
        $valid_moves7 = $this->check_available_squares($gridpositions,$x,$y,-1,1);  //left down
        $valid_moves8 = $this->check_available_squares($gridpositions,$x,$y,1,1);   //right down     
        
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4,$valid_moves5,$valid_moves6,$valid_moves7,$valid_moves8);
        
        return $valid_moves;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0,true);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0,true);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1,true);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1,true);   //down      
        $valid_moves5 = $this->check_available_squares($gridpositions,$x,$y,-1,-1,true);  //left up
        $valid_moves6 = $this->check_available_squares($gridpositions,$x,$y,1,-1,true);   //right up      
        $valid_moves7 = $this->check_available_squares($gridpositions,$x,$y,-1,1,true);  //left down
        $valid_moves8 = $this->check_available_squares($gridpositions,$x,$y,1,1,true);   //right down     
        
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4,$valid_moves5,$valid_moves6,$valid_moves7,$valid_moves8);

        $return_str = 'Queen moved ';
        $chess = $this->check_chess($gridpositions,$valid_moves);
        
        $chess_arr = null;
        if ($chess !== false) {
            $return_str .= ':chess (' . $chess[0] . '-' . $chess[1] .')';
            $chess_arr = array_slice($valid_moves,0,count($valid_moves));
        }
        
         return array($gridpositions,$return_str,$chess_arr);
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9819';
        }
        return '&#9813';
    } 
}
