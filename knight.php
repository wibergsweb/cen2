<?php
class Knight extends Piece {    
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
    
    public function check($gridpositions,$x,$y,$direction_x,$direction_y) {
        $vm = array();
        $xd = $x+$direction_x;
        $yd = $y+$direction_y;
        if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
            $check_piece = $gridpositions[$xd][$yd];
            if ($check_piece == null) {
                $vm = array($xd,$yd);
            }   
            if ($check_piece !==null && $check_piece->get_color() === $this->other_players_color) {
                $vm = array($xd,$yd);
            }
            if ($check_piece !==null && $check_piece instanceof Passant) {
                $vm = array($xd,$yd);
            }
            
        }
        
        return $vm;
    }    
    
    public function get_validmoves($gridpositions, $x,$y) {      
        $valid_moves = array();
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,-2);    
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,-2);
        $valid_moves[] = $this->check($gridpositions,$x,$y,-2,-1);
        $valid_moves[] = $this->check($gridpositions,$x,$y,2,-1);
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,2);
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,2);  
        $valid_moves[] = $this->check($gridpositions,$x,$y,2,1);  
        $valid_moves[] = $this->check($gridpositions,$x,$y,-2,1);                

        $temp = array();
        foreach($valid_moves as $vm) {
            if (!empty($vm)) {
                $temp[] = $vm;
            }
        }
        
        return $temp;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
         return array($gridpositions,'Knight moved');
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9822';
        }
        return '&#9816';
    } 
}
