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

    public function get_board() {
        return $this->boardobj;
    }

    public function get_status() {
        return $this->status;    
    }

    public function move_to($x1,$y1,$x2,$y2,$turn,$gridpos=null,$check_chess_function = false) {
        $this->status = '';
        $make_move = false;

        if ($this->check_mate === true) {
            $this->status = 'redo';
            if ($this->whos_turn == 0) {
                $this->whos_turn = 1;
            }
            else {
                $this->whos_turn = 0;
            }
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
        $valid_moves = $active_piece->get_validmoves($this->gridpos,$x1,$y1);                

        //Not this user's turn
        if (intval($turn) == intval($active_piece->get_color())) {
            $this->status .= 'Its not your turn!';  
            $this->status = 'redo';            
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


        if (isset($active_piece)) {            
            $is_chess = $active_piece->check_chess($this, $this->gridpos, $active_piece, $valid_moves, $x1, $y1, $x2, $y2);
            if ($is_chess['checkmate'] === 'yes') {
                $this->checkmate = true;
                $this->status .= "CHECKMATE FROM $x2,$y2!!!";  
                $this->boardobj->renew($this->gridpos);
                return $this;                          
            }
        }
                  
        
        if ($make_move == false) {
            if ($this->debug_mode === true) {
                $this->status .= '<h2>Invalid move. Nothing happens on board!</h2>';
            }
            else {
                $this->status = 'redo';
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
            $this->move_to($rook_movefrom_x,$rook_movefrom_y,$rook_moveto_x,$rook_moveto_y,$this->get_whosturn(),$this->gridpos);      
            $x2 = $rook_moveto_x;
            $y2 = $rook_moveto_y;
        }

        if (isset($active_piece)) {
            $active_piece->last_move($x1,$y1);
            $valid_moves = $active_piece->get_validmoves($this->gridpos, $x1, $y1);
            $is_chess = $active_piece->check_chess($this, $this->gridpos, $active_piece, $valid_moves, $x1, $y1, $x2, $y2);
            if ($is_chess['checkmate'] === 'yes') {
                $this->checkmate = true;
                $this->status .= "CHECKMATE!!!";  
                $this->boardobj->renew($this->gridpos);
                return $this;                          
            }

            if ($is_chess['makemove'] === 'no') {
                $this->status = 'redo'; //Your in chess after your move so you can not go here.';
                return $this;
            }
            if ($is_chess['checked'] === 'yes') {
                $this->status = "CHESS!!!!";
            }
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