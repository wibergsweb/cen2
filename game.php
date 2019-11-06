<?php
require_once('piece.php');
require_once('pawn.php');
require_once('rook.php');
require_once('knight.php');
require_once('bishop.php');
require_once('queen.php');
require_once('king.php');
require_once('passant.php');
require_once('board.php');

class Game {
    private $boardobj;
    private $whos_turn = 0;
    private $status = '';
    private $gridpos;
    private $debug_mode = true;
    private $checked_state = false;
       
    /*
     * $forward = -1 means white at bottom of board, 1 means white at top of board
     */
    public function __construct($forward = -1) {        
        $backward = -abs($forward);
        $this->boardobj = new board();
        $this->boardobj->game_start();       
    }

    public function get_status() {
        return $this->status;    
    }

    public function move_to($x1,$y1,$x2,$y2,$turn) {
        $this->status = '';
        $make_move = false;
        if ($this->debug_mode === true) {          
            $this->status .= '<br>x1=' . $x1 . ', y1=' . $y1;
            $this->status .= ' TO x2=' . $x2 . ', y2=' . $y2;
        }
        $this->gridpos = $this->boardobj->get_gridpositions();
        $active_piece = $this->boardobj->get_piece($x1,$y1); 
        $valid_moves = $active_piece->get_validmoves($this->gridpos,$x1,$y1,$x2,$y2);                

        //Not this user's turn
        if (intval($turn) == intval($active_piece->get_color())) {
            $this->status .= 'Its not your turn!';
            return $this;
        }

        if ($this->debug_mode === true) {
            $this->status .= '<pre>'.print_r($valid_moves,true).'<hr>';
            $this->status .= print_r($active_piece,true) . '</pre>';        
        }
        
        //Make sure player only are able to go to valid locations
        foreach($valid_moves as $vm) {
            $check_movetox = -1;
            $check_movetoy = -1;
            if (isset($vm[0])) {
                $check_movetox = $vm[0];
            }
            if (isset($vm[1])) {
                $check_movetoy = $vm[1];
            }
            if (($check_movetox == $x2) && ($check_movetoy == $y2)) {
                $make_move = true;  
                break;
            }
        }

        //Regenerate temporary gridpos (to make it possible to check if player is chess
        //after move without affecthing this actual object's gridpos)
        $temp_gridpos = array_slice($this->gridpos,0,count($this->gridpos));
        $temp_gridpos[$x1][$y1] = null;            //Set current square to null
        $temp_gridpos[$x2][$y2] = $active_piece;   //Set new grid to actual piece that was in current grid
        $after_move = $active_piece->get_aftermove($temp_gridpos,$x2,$y2);
        $temp_gridpos = array_slice($after_move[0],0,count($after_move[0]));

        //Go through whole board and check if some piece is
        //checking the king on the new position
        $found_checked = 0;
        for($yp=0;$yp<8;$yp++) {
            for($xp=0;$xp<8;$xp++) {
                $piece = $temp_gridpos[$xp][$yp];
                if ($piece !== null && !$piece instanceof King) {
                    //Get valid moves for each piece on board and check
                    //if any piece is checking this king
                    $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp, $x2,$y2);
                    
                    if (!empty($validmoves_piece)) {
                        $aftermove = $piece->get_aftermove($temp_gridpos,$xp,$yp);
                        if (!empty($aftermove)) {
                            if (stristr($aftermove[1],'chess') !== false) { 
                                //Only invalid if same player's piece
                                if (intval($turn) == intval($piece->get_color())) {
                                    $make_move = false;
                                    if ($this->debug_mode === true) {                                                        
                                        $this->status .= 'King is in chess. You have to move (or protect if possible) king.';
                                    } 
                                }   
                                else {
                                    if ($this->debug_mode === true) {                                                        
                                        $this->status .= '<strong>Check by ' . get_class($piece) . ' (not neccesarily directly)</strong><br>';
                                    } 

                                }                                                                                             
                                 
                                $found_checked++;
                            }
                        }
                    }
                }
            }
        }
        if ($found_checked == 0) {
            $this->checked_state = false;
        }

        if ($this->checked_state === false) {
            //Checked state is false
            if (stristr($after_move[1],'chess') !== false) {
                $this->checked_state = true;
            }
        }        

        if ($make_move == false) {
            if ($this->debug_mode === true) {
                $this->status .= '<h2>Invalid move. Nothing happens on board!</h2>';
            }
            return $this;
        }              

        $this->gridpos[$x1][$y1] = null;            //Set current grid to null
        $this->gridpos[$x2][$y2] = $active_piece;   //Set new grid to actual piece that was in current square
        
        $after_move = $active_piece->get_aftermove($this->gridpos,$x2,$y2);
        $this->status .= '<b>' .$after_move[1] .'</b>';    

        //Regenerate gridpos (after move)
        $this->gridpos = array_slice($after_move[0],0,count($after_move[0]));

        //Change whom's turn it is
        if ($this->whos_turn == 1) {
            $this->whos_turn = 0;
        }
        else {
            $this->whos_turn = 1;
        }

        if ($active_piece->get_waituser() === false) {
            $active_piece->last_move($x2,$y2);
            $active_piece->not_first_move();
            $this->boardobj->renew($this->gridpos);
            return $this;
        }     
        
        return $this;
        
    }

    public function get_whosturn() {
        return $this->whos_turn;
    }
    
    public function player_has_chosenpiece($piece,$x,$y) {        
        $piece->last_move($x2,$y2);
        $this->gridpos[$x][$y] = $piece;
        $this->boardobj->renew($this->gridpos);
        if ($this->debug_mode === true) {            
            $this->status .= 'User has selected piece now.';
        }
        return $this->draw();    
    }
    
    public function draw() {
        return $this->boardobj->output_html();
    }
    
    
}