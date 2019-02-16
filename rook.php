<?php
class Rook extends Piece {
    public $move_steps=1;
    public $other_players_color;
    
    public function __construct($color) {
        $this->color = $color;
        if ($this->color === 0) {
            $this->other_players_color = 1;
        }
        else {
            $this->other_players_color = 0;
        }            
    }
    
    public function get_validmoves($gridpositions, $x,$y) {                
        $valid_moves = array();
        
        //check left
        $this->multi_x  = -1;
        $this->multi_y  = 0;        
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y);           

        //check right
        $this->multi_x  = 1;
        $this->multi_y  = 0;        
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y);           

        //check up
        $this->multi_x  = 0;
        $this->multi_y  = -1;        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y);     
        
        //check down
        $this->multi_x  = 0;
        $this->multi_y  = 1;        
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y);   
        
        
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);
        
        return $valid_moves;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
         return array($gridpositions,'Rook moved');
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9820';
        }
        return '&#9814';
    }    
}
