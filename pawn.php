<?php
class Pawn extends Piece {    
    //threat() → change move_pattern  (forward=1, backward=0, left=1, right=1)
    public $forward = true;
    public $move_steps = 2;
    public $wait_user = false;
    public $main_direction = null; //Up or down on board?
    private $other_players_color = null;
    
    public function __construct($color, $main_direction) {
        $this->color = $color;
        if ($this->color === 0) {
            $this->other_players_color = 1;
        }
        else {
            $this->other_players_color = 0;
        }
        $this->main_direction = $main_direction;
    }
    
    public function not_first_move() {
        $this->first_move = false;
    }
    
    
    public function get_validmoves($gridpositions, $x,$y) {
        $this->forward = true;
        $direction = $this->main_direction;
        
        if ($this->first_move===false) {
            $this->move_steps = 1;
        }
        $valid_moves = array();
        //Check normal movepattern
        for($i=1;$i<$this->move_steps+1;$i++) {
            
            if ($direction == -1) {
                $check_piece = $gridpositions[$x][$y-$i];
                if ($check_piece == null) {
                    $valid_moves[] = array($x,$y-$i);
                }
            }
            else {
                $check_piece = $gridpositions[$x][$y+$i];            
                if ($check_piece == null) {
                    $valid_moves[] = array($x,$y+$i);
                }                
            }
            
        }
        

        
        
        $check_piece_diagonal_left = null;
        $check_piece_diagonal_right = null;
        if ($x<7) {
            $check_piece_diagonal_right = $gridpositions[$x+1][$y+$direction];
        }
        if ($x>0) {
            $check_piece_diagonal_left = $gridpositions[$x-1][$y+$direction];  
        }

        if ($check_piece_diagonal_left !== null) {
            $piece_color = $check_piece_diagonal_left->get_color();
            if ($piece_color===$this->other_players_color && !$check_piece_diagonal_left instanceof King) {
                $valid_moves[] = array($x-1,$y+$direction);
            }
        }

        if ($check_piece_diagonal_right !== null) {
            $piece_color = $check_piece_diagonal_right->get_color();
            if ($piece_color===$this->other_players_color && !$check_piece_diagonal_right instanceof King) {
                $valid_moves[] = array($x+1,$y+$direction);
            }
        }
        
        return $valid_moves;
    }
    
    
    public function get_aftermove($gridpositions, $x,$y) {
        $direction = $this->main_direction;
        if ($y>0) {
            $check_piece_diagonal_left = null;
            $check_piece_diagonal_right = null;
            if ($x>0 && $x<7 && $y>0 && $y<7) {
                $check_piece_diagonal_left = $gridpositions[$x-1][$y+$direction];
                $check_piece_diagonal_right = $gridpositions[$x+1][$y+$direction];
            }
            
            if ($check_piece_diagonal_left !== null) {
                $piece_color = $check_piece_diagonal_left->get_color();
                if ($piece_color===$this->other_players_color && $check_piece_diagonal_left instanceof King) {
                    return 'chess';
                }
            }

            if ($check_piece_diagonal_right !== null) {
                $piece_color = $check_piece_diagonal_right->get_color();
                if ($piece_color===$this->other_players_color && $check_piece_diagonal_right instanceof King) {
                    return 'chess';
                }
            }                 
        }
        if ($y===0 && $direction === -1) {
            $this->wait_user = true; //Wait for user to select piece
            return 'Choose your piece';
        }
        if ($y===7 && $direction === 1) {
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
