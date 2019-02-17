<?php
class Knight extends Piece {    
    public $move_steps=1;

    public function __construct($color, $main_direction=null) {
        $this->color = $color;
        if ($this->color === 0) {
            $this->other_players_color = 1;
        }
        else {
            $this->other_players_color = 0;
        }
        $this->main_direction = $main_direction;
    }    
    
    public function check($gridpositions,$x,$y,$direction_x,$direction_y,$king_check=false) {
        $vm = array();
        $xd = $x+$direction_x;
        $yd = $y+$direction_y;
        if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
            $check_piece = $gridpositions[$xd][$yd];
            if ($check_piece == null) {
                $vm = array($xd,$yd);
            }   
                if ($king_check === false) {
                    if ($check_piece !==null && $check_piece instanceof King) {
                        //King's square is not included. Cannot go further                       
                        return $vm;
                    }
                }
                else {
                    //Other players king is included, but no more valid moves in this direction
                    if ($check_piece !== null && $check_piece->get_color() === $this->other_players_color) {
                        $vm = array($xd,$yd); 
                        return $vm;          
                    }
                }
                
            if ($check_piece !==null && $check_piece->get_color() === $this->other_players_color && !$check_piece instanceof King) {
                $vm = array($xd,$yd);
            }
            if ($check_piece !==null && $check_piece instanceof Passant && !$check_piece instanceof King) {
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
        $valid_moves = array();
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,-2,true);    
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,-2,true);
        $valid_moves[] = $this->check($gridpositions,$x,$y,-2,-1,true);
        $valid_moves[] = $this->check($gridpositions,$x,$y,2,-1,true);
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,2,true);
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,2,true);  
        $valid_moves[] = $this->check($gridpositions,$x,$y,2,1,true);  
        $valid_moves[] = $this->check($gridpositions,$x,$y,-2,1,true);    

        $return_str = 'Knight moved ';
        $chess = $this->check_chess($gridpositions,$valid_moves);
        if ($chess !== false) {
            $return_str .= ':chess (' . $chess[0] . '-' . $chess[1] .')';
        }
        
         return array($gridpositions,$return_str);
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9822';
        }
        return '&#9816';
    } 
}
