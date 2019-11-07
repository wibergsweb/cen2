<?php
class King extends Piece {
    public $is_chess = false;
    
    public function is_chess() {
        $this->is_chess = true;
    } 
    
    public function check($gridpositions,$x,$y,$direction_x,$direction_y) {
        $vm = array();
        $xd = $x+$direction_x;
        $yd = $y+$direction_y;
        if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
            $check_piece = $gridpositions[$xd][$yd];
            if ($check_piece === null) {
                $vm = array($xd,$yd);
            }   
            else if ($check_piece !==null && $check_piece instanceof Passant) {
                $vm = array($xd,$yd);
            }
            else if ($check_piece !== null && $this->get_color() != $check_piece->get_color()) {
                $vm = array($xd, $yd);
            }
        }
        
        return $vm;
    }
    
    public function get_validmoves($gridpositions, $x,$y,$x2,$y2,$check_other_players_color=true) {    
        $valid_moves = array();
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,0);       //left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,0);        //right
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,1);        //check down
        $valid_moves[] = $this->check($gridpositions,$x,$y,0,-1);       //check up
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,-1);      //check up left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,-1);       //check up right
        $valid_moves[] = $this->check($gridpositions,$x,$y,-1,1);       //check down left
        $valid_moves[] = $this->check($gridpositions,$x,$y,1,1);        //check down right      
        
        //Castling
        $this->castling = false;
        if ($this->get_firstmove() === true) {
            if ($y==0 || $y==7 ) {                
                if ($x2 == $x+2) {       
                    $rook = $gridpositions[7][$y]; 
                    if ($rook->get_firstmove() === true) {
                        $this->castling = true;
                        $valid_moves[] = array($x+2,$y); //Short castling (to the right)
                        $this->rookpos = array(7,$y,$x+1,$y); //First two values are current pos of rook. Rook should be set to the left of king
                    }                              
                }
                if ($x2 == $x-2) {
                    $rook = $gridpositions[0][$y];
                    if ($rook->get_firstmove() === true) {
                        $this->castling = true;
                        $valid_moves[] = array($x-2,$y); //Long castling (to the left)
                        $this->rookpos = array(0,$y,$x-1,$y); //First two values are current pos of rook. Rook should be set to the right of king
                    }
                }
            }
        }

        //Valid moves for the other king is NOT valid moves for this king
        //(Make sure kings cannot stand besides eachother)
        $king = $gridpositions[$x][$y];
        $fake_gridpositions = array_slice($gridpositions,0,count($gridpositions));        
        $fake_gridpositions[$x2][$y2] = $king; //Check if valid now when king is on new position
        $fake_gridpositions[$x][$y] = null; //Make sure not check this gridposition

        for($yp=0;$yp<8;$yp++) {
            for($xp=0;$xp<8;$xp++) {
                $piece = $fake_gridpositions[$xp][$yp];
                if ($piece !== null) {
                    if ($piece instanceof King && $this->get_color() != $piece->get_color()) {                        
                        $otherplayers_king_x = $xp;
                        $otherplayers_king_y = $yp;
                        
                        //We use the original gridpositions here because we need to check valid 
                        //moves for other king before movement of this king
                        $validmoves_otherking = array();
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,-1,0);       //left
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,1,0);        //right
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,0,1);        //check down
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,0,-1);       //check up
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,-1,-1);      //check up left
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,1,-1);       //check up right
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,-1,1);       //check down left
                        $validmoves_otherking[] = $this->check($gridpositions,$xp,$yp,1,1);        //check down right   
                        break;
                    }
                }
            }
        }

        foreach($valid_moves as $vm_key => $vm) {
            if (!empty($vm)) {
                $vmx = $vm[0];
                $vmy = $vm[1];

                //Check in other king's valid moves and remove
                //from this kings valid moves if they exist for this king
                //(because king cannot stand beside the other king)
                foreach($validmoves_otherking as $vmok_key => $vmok) {
                    if (!empty($vmok)) {
                        $vmok_x = $vmok[0];
                        $vmok_y = $vmok[1];
                        if ($vmok_x == $vmx && $vmok_y == $vmy) {
                            unset($valid_moves[$vm_key]);
                        }
                    }
                }                
            }
        }

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
