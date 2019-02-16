<?php
class Pawn extends Piece {    
    public $forward = true;
    public $move_steps = 2;
    public $wait_user = false;
    public $main_direction = null;  //Up (-1) or down (1) on board?
    public $passant_square = null;
    public $last_move = array();     //array of x,y
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
    
    
    public function get_firstmove() {
        return $this->first_move;
    }

    
    public function get_last_move() {
        return $this->last_move;
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
        if ($x>0 && $x<7) {
            $check_piece_diagonal_right = $gridpositions[$x+1][$y+$direction];
            $check_piece_diagonal_left = $gridpositions[$x-1][$y+$direction];  
        }

        if ($check_piece_diagonal_left !== null) {
            
            $piece_color = $check_piece_diagonal_left->get_color();
            if ($piece_color===$this->other_players_color && !$check_piece_diagonal_left instanceof King) {
                $is_valid = true;   
                
                if ($check_piece_diagonal_left instanceof Passant) {
                    $is_valid = false;
                    
                    //Check left piece (is it other players pawn?)
                    if ($piece_color===$this->other_players_color && $gridpositions[$x-1][$y] instanceof Pawn) {
                        //Previous move in game (other players pawn)
                        $last = $gridpositions[$x-1][$y]->get_last_move();
                        $last_x = $last[0];
                        $last_y = $last[1];
                        if ($x-1 === $last_x && $y == $last_y) {
                            $is_valid = true;
                        }
                    }
                }
                
                if ($is_valid === true) {
                    $valid_moves[] = array($x-1,$y+$direction);
                }
                
            }
        }

        if ($check_piece_diagonal_right !== null) {
            $piece_color = $check_piece_diagonal_right->get_color();
            if ($piece_color===$this->other_players_color && !$check_piece_diagonal_right instanceof King) {
                $is_valid = true;   
                
                if ($check_piece_diagonal_right instanceof Passant) {
                    $is_valid = false;
                    
                    //Check left piece (is it other players pawn?)
                    if ($piece_color===$this->other_players_color && $gridpositions[$x+1][$y] instanceof Pawn) {
                            //Previous move in game (other players pawn)
                            $last = $gridpositions[$x+1][$y]->get_last_move();
                            $last_x = $last[0];
                            $last_y = $last[1];
                            if ($x+1 === $last_x && $y == $last_y) {
                                $is_valid = true;
                            }
                    }
                }
                
                if ($is_valid === true) {
                    $valid_moves[] = array($x+1,$y+$direction);
                }
            }
        }
        
        return $valid_moves;
    }
    
    
    public function get_aftermove($gridpositions, $x,$y) {
        
        $direction = $this->main_direction;
        if ($direction==1) {
            $other_direction = -1;
        }
        else {
            $other_direction = 1;
        }        
        $check_piece_diagonal_left = null;
        $check_piece_diagonal_right = null;
        if ($x>0 && $x<7 && $y>0 && $y<7) {
            $check_piece_diagonal_left = $gridpositions[$x-1][$y+$direction];
            $check_piece_diagonal_right = $gridpositions[$x+1][$y+$direction];
            
            if ($check_piece_diagonal_left !== null) {
                $piece_color = $check_piece_diagonal_left->get_color();
                if ($piece_color===$this->other_players_color && $check_piece_diagonal_left instanceof King) {
                    $gridpositions[$x-1][$y+$direction]->is_chess();
                    return array($gridpositions,'chess');
                }
            }                

            if ($check_piece_diagonal_right !== null) {
                $piece_color = $check_piece_diagonal_right->get_color();
                if ($piece_color===$this->other_players_color && $check_piece_diagonal_right instanceof King) {
                    $gridpositions[$x+1][$y+$direction]->is_chess();
                    return array($gridpositions,'chess');
                }               
            }                 
        }
        if ($y===0 && $direction === -1) {
            $this->wait_user = true; //Wait for user to select piece
            return 'Choose your piece';
        }
        if ($y===7 && $direction === 1) {
            $this->wait_user = true; //Wait for user to select piece
            return array($gridpositions,'Choose your piece');
        }     
        
        if ($this->move_steps==2) {
            $this->passant_square = array($this->color, $x,$y+($other_direction));
        }
        else {
            $this->passant_square = null;
        }
            
        $passant = $gridpositions[$x][$y]->get_passantsquare();
        if ($passant !== null) {
            $passant_color = $passant[0];
            $passant_x = $passant[1];
            $passant_y = $passant[2];
            $gridpositions[$passant_x][$passant_y] = new Passant($passant_color);            
        }
        else {            
            for($yc=0;$yc<8;$yc++) {
                for($xc=0;$xc<8;$xc++) {
                    $gp = $gridpositions[$xc][$yc];
                    if ($gp instanceof Passant) {
                        $gridpositions[$xc][$yc] = null;
                    }                     
                }
            }
            //Remove pawn
            $movepos_y = $y+$other_direction;
            $gridpositions[$x][$movepos_y] = null;
        }
        
        return array($gridpositions,'Pawn moved');
    }
    
    public function get_passantsquare() {
        return $this->passant_square;
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
