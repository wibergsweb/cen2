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
            if ($check_piece !==null && $check_piece instanceof Passant) {
                $vm = array($xd,$yd);
            }
            
        }
        
        return $vm;
    }
    
    public function get_validmoves($gridpositions, $x,$y,$x2,$y2,$check_other_players_color=true) {    
        $valid_moves = array();
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,0);       //left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,0);        //right
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,1);        //check up
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,-1);       //check down
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,-1);      //check up left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,-1);       //check up right
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,1);       //check down left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,1);        //check down right      
        
        //Is king chess when king has moved?
        $king = $gridpositions[$x][$y];
        $fake_gridpositions = array_slice($gridpositions,0,count($gridpositions));        
        $fake_gridpositions[$x2][$y2] = $king; //Check if valid now when king is on new position
        $fake_gridpositions[$x][$y] = null; //Make sure not check this gridposition

        //Go through whole board and check if some piece is
        //checking the king on the new position
        for($yp=0;$yp<8;$yp++) {
            for($xp=0;$xp<8;$xp++) {
                $piece = $fake_gridpositions[$xp][$yp];
                if ($piece !== null && !$piece instanceof King) {

                    //Get valid moves for each piece on board and check
                    //if any piece is checking this king
                    $validmoves_piece = $piece->get_validmoves($fake_gridpositions, $xp, $yp, 0,0);
                    
                    //Only check more if there are any valid moves piece
                    //in current fake gridpositions
                    if (!empty($validmoves_piece)) {
                        $aftermove = $piece->get_aftermove($fake_gridpositions,$xp,$yp);
                        if (!empty($aftermove)) {
                            if (stristr($aftermove[1],'chess') !== false) {
                                //IF above position (Where piece checks king) is included
                                //in valid moves, then remove this validity
                                //$x2 and $y2 is the king's position in grid
                                foreach($valid_moves as $vm_key => $vm) {
                                    if (!empty($vm)) {
                                        $vmx = $vm[0];
                                        $vmy = $vm[1];

                                        if ($x2 == $vmx && $y2 == $vmy) {
                                            unset($valid_moves[$vm_key]);
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
