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
    private $debug_mode = false;
    private $checked_state = false;
    private $check_mate = false;
       
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

    public function move_to($x1,$y1,$x2,$y2,$turn,$gridpos=null,$check_chess_function = false) {
        $this->status = '';
        $make_move = false;

        if ($this->check_mate === true) {
            return $this;
        }

        if ($this->debug_mode === true) {          
            $this->status .= '<br>x1=' . $x1 . ', y1=' . $y1;
            $this->status .= ' TO x2=' . $x2 . ', y2=' . $y2;
        }

        if ($gridpos === null) {
            $this->gridpos = $this->boardobj->get_gridpositions();
        }
        else if (is_array($gridpos)) {
            $this->gridpos = array_slice($gridpos,0,count($gridpos));
        }

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
                if ($piece !== null && !$piece instanceof King && $piece->get_color() != $this->get_whosturn()) {
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
                                        if (get_class($piece) != get_class($active_piece)) {                                                   
                                            $this->status .= '<strong>Check by ' . get_class($piece) . ' (not directly)</strong><br>';
                                        }
                                    } 

                                }                                                                                             
                                 
                                $found_checked++;
                            }
                        }
                    }
                }
            }
        }
        $possible_moves = 1; //Possible moves for king if not going through any of loops below...

        if ($found_checked == 0) {
            $this->checked_state = false;
        }
        else if ($found_checked>0) {
            
           
            $king = null;
            for($yp=0;$yp<8;$yp++) {
                for($xp=0;$xp<8;$xp++) {                    
                    $piece = $temp_gridpos[$xp][$yp];                   
                    if ($piece !== null && $piece instanceof King && $piece->get_color() == $this->get_whosturn()) {                        
                        $king = $piece;
                        $king_x = $xp;
                        $original_king_x = $xp;
                        $king_y = $yp;
                        $original_king_y = $yp;
                        $validmoves_king = $piece->get_validmoves($temp_gridpos,$xp,$yp,$xp,$yp);                        
                        break;
                    }
                }
            }

            if ($this->debug_mode === true) {
                $this->status .= 'kings position: ' . $king_x . ',' . $king_y .'<br>';
                $this->status .= 'valid moves of king (before actual move): ' . print_r($validmoves_king,true);
            }

            $this->status .= '<hr>';
           
            $temp_gridpos[$king_x][$king_y] = null;

            //King is checked... Check if king is check mate
                
            //If every possible move for king is chess
            //then it's check mate
            $possible_moves = count($validmoves_king);
            $this->status .= 'possible moves=' . $possible_moves . '<br>';

            $temp_gridpos[$x1][$y1] = null;            //Set current grid to null
            $temp_gridpos[$x2][$y2] = $active_piece;   //Set new grid to actual piece that was in current square
            
            foreach($validmoves_king as $vmk) {
                $king_moveto_x = $vmk[0];
                $king_moveto_y = $vmk[1];
                         
                $temp_gridpos[$king_moveto_x][$king_moveto_y] = $king; 
                $temp_gridpos[$king_x][$king_y] = null;
                   
                for($yp=0;$yp<8;$yp++) {
                    for($xp=0;$xp<8;$xp++) {
                        $piece = $temp_gridpos[$xp][$yp];
                        
                        if ($piece !== null && !$piece instanceof King && $this->get_whosturn() != $piece->get_color()) {
                            $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp, $king_moveto_x, $king_moveto_y);                    
                                if (!empty($validmoves_piece)) {
                                $aftermove = $piece->get_aftermove($temp_gridpos,$xp,$yp);
                                if (!empty($aftermove)) {
                                    if (stristr($aftermove[1],'chess') !== false) { 
                                        $possible_moves--;
                                        //if ($this->debug_mode === true) {
                                            $this->status .= 'check from position: ' . $xp .','.$yp . '<br>';
                                            $this->status .= 'possible moves: ' . $possible_moves . '<br>';
                                            $this->status .= '<hr>';
                                        //}
                                    }
                                }
                            }
                        }
                    }
                }                
                
                $king_x = $king_moveto_x;
                $king_y = $king_moveto_y;
            }

        
        


            if (intval($possible_moves) <= 0) {
                //The king cannot move. Is it possible to attacking player
                //with another piece?
                $attacker_can_be_removed = false;

                for($yp=0;$yp<8;$yp++) {
                    for($xp=0;$xp<8;$xp++) {
                        $piece = $temp_gridpos[$xp][$yp];                        
                        if ($piece !== null && !$piece instanceof King && $this->get_whosturn() == $piece->get_color()) {
                            $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp, $x2, $y2);                                      
                            if (!empty($validmoves_piece)) {
                                foreach($validmoves_piece as $vmk) {  
                                    if (isset($vmk[0]) && isset($vmk[1])) { 
                                        $xk = $vmk[0];
                                        $yk = $vmk[1];    
                                        if ($xk == $x2 && $yk == $y2) {
                                            //If Pawn is attacking it must be diagonally
                                            if ($piece instanceof Pawn) {
                                                if ($xk == $x2) {
                                                    continue;
                                                }
                                            }
                                            $this->status .= 'attacker can be removed by another piece.<br>';
                                            $attacker_can_be_removed = true;
                                            break;
                                        }                       
                                    }
                                }
                            }
                        }
                    }
                }

                if ($attacker_can_be_removed === false) {
                    $this->status .= '<b>CHESS MATE!</b>'; 
                    $this->check_mate = true; 
                    $make_move = false;
                }

            }
            

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

        //Is castling? (Move rook when king has moved?)
        if ($active_piece->castling === true) {
            $movecastling_arr = array_slice($active_piece->rookpos,0,count($active_piece->rookpos));
            $rook_movefrom_x = $active_piece->rookpos[0];
            $rook_movefrom_y = $active_piece->rookpos[1];
            $rook_moveto_x = $active_piece->rookpos[2];
            $rook_moveto_y = $active_piece->rookpos[3];
            
            //Move actual rook.
            $rook = $this->gridpos[$rook_movefrom_y][$rook_movefrom_y];
            $this->gridpos[$rook_movefrom_x][$rook_movefrom_y] = null;
            $this->gridpos[$rook_moveto_x][$rook_moveto_y] = $rook;
            $active_piece->castling = false; //Make sure not eternity loop
            $this->whos_turn = ($this->whos_turn == 0 ? 1 : 0); 
            $this->move_to($rook_movefrom_x,$rook_movefrom_y,$rook_moveto_x,$rook_moveto_y,$this->get_whosturn(),$this->gridpos);      
        }

        //Change whom's turn it is
        $this->whos_turn = ($this->whos_turn == 0 ? 1 : 0);        

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