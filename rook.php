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
    
    public function check($gridpositions,$x,$y,$direction_x,$direction_y) {
        $vm = array();
        $xd = $x+$direction_x;
        $yd = $y+$direction_y;
        echo '<hr>';
        echo 'xd=' . $xd;
        echo 'yd='.$yd;
        echo '<hr>';
        if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
            $check_piece = $gridpositions[$xd][$yd];
            var_dump($check_piece);
            if ($check_piece === null) {
                $vm = array($xd,$yd);
            }   
            if ($check_piece !==null && $check_piece->get_color() === $this->other_players_color) {
                $vm = array($xd,$yd);
            }
            if ($check_piece !==null && $check_piece->get_color() === $this->get_color() && !$check_piece instanceof Passant) {
                return null;
            }            
            if ($check_piece !==null && $check_piece instanceof Passant) {
                $vm = array($xd,$yd);
            }            
        }
        else {
            return null;
        }

        
        return $vm;
    }
    
    public function get_validmoves($gridpositions, $x,$y) {                
        $valid_moves = array();
        /*
         * $i=1;
        while($i<8) {
            $valid_moves[] = $this->check($gridpositions,$x,$y,-1*$i,0);       //left
            $i++;
        }
        $i=1;
        while($i<8) {        
            $valid_moves[] = $this->check($gridpositions,$x,$y,1*$i,0);        //right
            $i++;
        }
        */
        $i=1;
        $do_check = true;
        while($do_check === true) {
            $vm = $this->check($gridpositions,$x,$y,0,-1*$i);        //check up
            if ($vm === null ) {
                $do_check = false;
                break;
            }
            $valid_moves[] = $vm;
            $i++;
        }
        /*
        $i=1;
        while($i<8) {
            $valid_moves[] = $this->check($gridpositions,$x,$y,0,-1*$i);       //check down            
            $i++;
        }        
           */
        
        $temp = array();
        foreach($valid_moves as $vm) {
            if (!empty($vm)) {
                $temp[] = $vm;
            }
        }
        
        return $temp;
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
