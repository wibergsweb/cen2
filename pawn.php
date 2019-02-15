<?php
class Pawn extends Piece {    
    //threat() → change move_pattern  (forward=1, backward=0, left=1, right=1)
    
    public $forward_diagonal_left = false;
    public $forward_diagonal_right = false;
    public $forward = true;
    public $move_steps = 2;
    public $wait_user = false;
    
    public function __construct($color) {
            $this->color = $color;
    }

    
    public function not_first_move() {
        $this->first_move = false;
    }
    
    
    public function get_validmoves($gridpositions, $x,$y) {
        $this->forward = true;
         
        if ($this->first_move===false) {
            $this->move_steps = 1;
        }
        $valid_moves = array();
        //Check normal movepattern
        for($i=1;$i<$this->move_steps+1;$i++) {
            $check_piece = $gridpositions[$x][$y-$i];
            if ($check_piece == null) {
                $valid_moves[] = array($x,$y-$i);
            }
        }
        

        $check_piece_diagonal_left = $gridpositions[$x-1][$y-1];
        $check_piece_diagonal_right = $gridpositions[$x+1][$y-1];

        if ($check_piece_diagonal_left !== null) {
            $piece_color = $check_piece_diagonal_left->get_color();
            if ($piece_color===0 && !$check_piece_diagonal_left instanceof King) {
                $valid_moves[] = array($x-1,$y-1);
            }
        }

        if ($check_piece_diagonal_right !== null) {
            $piece_color = $check_piece_diagonal_right->get_color();
            if ($piece_color===0 && !$check_piece_diagonal_right instanceof King) {
                $valid_moves[] = array($x+1,$y-1);
            }
        }
        
        return $valid_moves;
    }
    
    
    public function get_aftermove($gridpositions, $x,$y) {
        if ($y>0) {
            $check_piece_diagonal_left = $gridpositions[$x-1][$y-1];
            $check_piece_diagonal_right = $gridpositions[$x+1][$y-1];

            if ($check_piece_diagonal_left !== null) {
                $piece_color = $check_piece_diagonal_left->get_color();
                if ($piece_color===0 && $check_piece_diagonal_left instanceof King) {
                    return 'chess';
                }
            }

            if ($check_piece_diagonal_right !== null) {
                $piece_color = $check_piece_diagonal_right->get_color();
                if ($piece_color===0 && $check_piece_diagonal_right instanceof King) {
                    return 'chess';
                }
            }                 
        }
        if ($y===0) {
            $this->wait_user = true; //Wait for user to select piece
            return 'Choose your piece';
        }
        
        return 'OK';
    }
    
    public function get_waituser() {
        return $this->wait_user;
    }
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9823';
        }
        return '&#9817';
    }
    

    
}
