<?php
abstract class Piece {
    public $first_move = true;
    public $color; //0 = black, 1 = white
    public $last_move;
    public $other_players_color = null;    
    public $main_direction = null;  //Up (-1) or down (1) on board?
    
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
    
    public function get_color() {
        return $this->color;
    }
    
    public function get_other_players_color() {
        return $this->other_players_color;
    }
    
    
    public function check_chess($gridpositions, $valid_moves) {
        foreach($valid_moves as $vm) {
            if (isset($vm[0]) && isset($vm[1])) {
                $x = $vm[0];
                $y = $vm[1];
                $grid = $gridpositions[$x][$y];
                if ($grid !== null && $grid instanceof King) {
                    return array($x,$y);
                }
            }
        }   
        return false;
    }
    
    
    public function check_available_squares($gridpositions,$x,$y,$multi_x,$multi_y,$king_check = false,$check_other_players_color=true) {
        $vm = array();
        $xd = $x + $multi_x;
        $yd = $y + $multi_y;
        
        $do_check = true;
        while($do_check === true) {   
            if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
                $check_piece = $gridpositions[$xd][$yd];

                if ($check_piece === null) {
                    $vm[] = array($xd,$yd);
                }   
                
                if ($king_check === false) {
                    if ($check_piece !==null && $check_piece instanceof King) {
                        //King's square is not included. Cannot go further
                        $do_check = false;
                        break;
                    }
                }
                else {
                    //Other players king is included, but no more valid moves in this direction
                    if ($check_piece !== null && $check_piece->get_color() === $this->other_players_color) {
                        $vm[] = array($xd,$yd); 
                        $do_check = false;
                        break;                    
                    }
                }
                
                if ($check_piece !==null && $check_piece->get_color() === $this->other_players_color && !$check_piece instanceof King) {
                    $vm[] = array($xd,$yd);
                    //Other player is included, but cannot go further
                    $do_check = false;
                    break;
                }
                if ($check_piece !==null && $check_piece->get_color() === $this->get_color() && !$check_piece instanceof Passant && !$check_piece instanceof King) {
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
        
            $xd+=$multi_x;
            $yd+=$multi_y;
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
    abstract public function get_validmoves($gridpositions, $x,$y,$x2,$y2,$check_other_players_color);
    
    //What to do after the actual move?
    abstract public function get_aftermove($gridpositions, $x,$y);
    
    //Get chess character
    abstract protected function get_char();
    
}
