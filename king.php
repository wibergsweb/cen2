<?php
class King extends Piece {
    public $is_chess = false;
    public $temp_valid_moves = array();
    
    public function is_chess() {
        $this->is_chess = true;
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
                //Is threatened by any other piece (other color) at this square in grid/in board? 
                //TODO check
                
                //If NOT threatened, it's valid:
                $vm = array($xd,$yd);
            }
            if ($check_piece !==null && $check_piece instanceof Passant) {
                $vm = array($xd,$yd);
            }
            
        }
        
        return $vm;
    }
    
    public function get_validmoves($gridpositions, $x,$y,$x2,$y2) {    
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

        //Is king chess when king has moved?
        $fake_gridpositions = array_slice($gridpositions,0,count($gridpositions));
        $king = $gridpositions[$x][$y];
        $fake_gridpositions[$x][$y] = null;
        $fake_gridpositions[$x2][$y2] = $king;
        
        for($yp=0;$yp<8;$yp++) {
            for($xp=0;$xp<8;$xp++) {
                $gp = $fake_gridpositions[$xp][$yp];
               
                if ($x2 != $xp && $y2 != $yp) { //Don't check this grid because this king is on this grid. The king cannot check
                    if ($gp !== null && $this->get_color() == $gp->get_other_players_color()) {
                            $piece = $gp->get_aftermove($fake_gridpositions,$xp,$yp);
                            
                            //Piece might be checking this king
                            if (isset($piece[2])) {
                                if ($piece[2] !== null) {
                                    foreach($piece[2] as $p) {
                                        $xc = $p[0];
                                        $yc = $p[1];
                                            //Check if x (that king is moving to) and y (that king is moving to) is equal 
                                            //to any square in grid
                                            foreach($temp as $tempkey=>$t) {
                                                if ($t[0] == $x2 && $t[1] == $y2) {
                                                    unset($temp[$tempkey]); //Remove from valid moves     
                                                }
                                            }
                                    }
                                }   
                            }
                    }
                }
            }
        }
        //End Is king chess when king has moved?
        
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
