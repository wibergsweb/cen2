<?php
abstract class Piece {
    public $first_move = true;
    public $color; //0 = black, 1 = white
    public $last_move;
    public $multi_x;
    public $multi_y;
    

    public function get_color() {
        return $this->color;
    }
    
    
    public function check_available_squares($gridpositions,$x,$y) {
        $vm = array();
        $xd = $x + $this->multi_x;
        $yd = $y + $this->multi_y;
        
        $do_check = true;
        while($do_check === true) {   
            if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
                $check_piece = $gridpositions[$xd][$yd];

                if ($check_piece === null) {
                    $vm[] = array($xd,$yd);
                }   
                if ($check_piece !==null && $check_piece->get_color() === $this->other_players_color) {
                    $vm[] = array($xd,$yd);
                    //Other player is included, but cannot go further
                    $do_check = false;
                    break;
                }
                if ($check_piece !==null && $check_piece->get_color() === $this->get_color() && !$check_piece instanceof Passant) {
                    $do_check = false;
                    break;
                }            
                if ($check_piece !==null && $check_piece instanceof Passant) {
                    $vm[] = array($xd,$yd);
                }            
            }
            else {
                //Outside chessboard
                $do_check = false;
                break;
            }
        
            $xd+=$this->multi_x;
            $yd+=$this->multi_y;
        }

        
        return $vm;
    }

    
    public function last_move($x,$y) {
        $this->last_move = array($x,$y);
    }
    
    public function not_first_move() {        
        $this->first_move = false;
    }
    
    public function get_waituser() {
        return false;
    }
    
    //This validation is done in each piece-class (pawn, knight, bishop etc)
    abstract public function get_validmoves($gridpositions, $x,$y);
    
    //Get chess character
    abstract protected function get_char();
    
}
