<?php
class King extends Piece {
    public $is_chess = false;
    public $move_steps=1;
    
    public function __construct($color) {
            $this->color = $color;
    }
    
    public function is_chess() {
        $this->is_chess = true;
    } 
    
    public function check($gridpositions,$x,$y,$direction_x,$direction_y) {
        $vm = array();
        $xd = $x+$direction_x;
        $yd = $y+$direction_y;
        if ($xd>0 && $xd<7 && $yd>0 && $yd<7) {
            $check_piece = $gridpositions[$xd][$yd];
            if ($check_piece == null) {
                $vm = array($xd,$yd);
            }   
        }
        
        return $vm;
    }
    
    public function get_validmoves($gridpositions, $x,$y) {                
        $valid_moves = array();
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,0);       //left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,0);        //right
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,1);        //check up
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,-1);       //check down
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,-1);      //check up left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,-1);       //check up right
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,1);       //check down left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,1);        //check down right      
        
        $temp = array();
        foreach($valid_moves as $vm) {
            if (!empty($vm)) {
                $temp[] = $vm;
            }
        }
        
        return $temp;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
         return array($gridpositions,'King moved');
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9818';
        }
        return '&#9812';
    } 
}
