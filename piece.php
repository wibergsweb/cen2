<?php
abstract class Piece {
    private $first_move = true;
    public $castling = false;
    public $rookpos = array();
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

    public function get_firstmove() {
        return $this->first_move;
    }
    
    public function get_other_players_color() {
        return $this->other_players_color;
    }    
    
    public function check_chess(Game $game = null, $gridpositions, $active_piece = null, $valid_moves = null, $x1 = 0, $y1 = 0, $x2 = 0, $y2 = 0) {
        $checked_state = "no";
        $checkmate = "no";
        $make_move ="yes";     
        $remove_piece = "no";     
        
        //Regenerate temporary gridpos (to make it possible to check if player is chess
        //after move without affecthing this actual object's gridpos)
        $temp_gridpos = array_slice($gridpositions,0,count($gridpositions));        
        $orignal_gridpos = array_slice($temp_gridpos,0,count($temp_gridpos));

        //Go through whole board and check if some piece is
        //checking the king on the new position
        $found_checked = 0;
        for($yp=0;$yp<8;$yp++) {
            for($xp=0;$xp<8;$xp++) {
                $piece = $temp_gridpos[$xp][$yp];
                if ($piece !== null && !$piece instanceof King) { //IMPORTANT. DONT CHECK COLOR HERE!!!!
                    //Get valid moves for each piece on board and check
                    //if any piece is checking this king
                    $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp);

                    if (count($validmoves_piece)>0) {
                        error_log("possible checkerpiece at $xp,$yp" ."\r\n",3,'checks.log');
                    }

                    foreach($validmoves_piece as $vmp) {
                        if (isset($vmp[0]) && isset($vmp[1])) {
                            $xgrid = $vmp[0];
                            $ygrid = $vmp[1];  
                            $cp = $temp_gridpos[$xgrid][$ygrid];
                            

                            if ($cp !== null) {
                                if ($game !== null) {
                                if ($cp->get_color() == $game->get_whosturn()) {
                                    error_log("same color $xp,$yp and $xgrid,$ygrid" ."\r\n",3,'checks.log');
                                    break; //Same color. if some piece of same color is in it's way, just break out of this loop
                                }  
                            }

                                if ($cp instanceof King) {
                                    error_log("piece from $xp,$yp is checking $xgrid,$ygrid" ."\r\n",3,'checks.log');
                                    $checked_state = "yes";
                                    $found_checked++;
                                    if ($game === null) {
                                        return array($xgrid,$ygrid);
                                    }

                                }

                            }
                        }
                    }
                    
                }
            }
        }
        $possible_moves = 1; //Possible moves for king if not going through any of loops below...

        

        if ($found_checked == 0) {
            $checked_state = "no";

            $check_state = array();
            $check_state['move'] = array($x1,$y1,$x2,$y2);
            $check_state['checked'] = $checked_state;
            $check_state['checkmate'] = $checkmate;
            $check_state['makemove'] = $make_move;    
            $check_state['possibletoremovepiece'] = $remove_piece;      
             
            error_log('checked state='. print_r($check_state,true),3,'checks.log');
            return $check_state;

            
        }
        else if ($found_checked>0) {  
                  
            $king = null;
            for($yp=0;$yp<8;$yp++) {
                for($xp=0;$xp<8;$xp++) {                    
                    $piece = $temp_gridpos[$xp][$yp];                   
                    if ($piece !== null && $piece instanceof King && $piece->get_color() == $game->get_whosturn()) {                        
                        $king = $piece;
                        $king_x = $xp;
                        $original_king_x = $xp;
                        $king_y = $yp;
                        $original_king_y = $yp;
                        $validmoves_king = $piece->get_validmoves($temp_gridpos,$xp,$yp);                        
                        break;
                    }
                }
            }
           
            $temp_gridpos[$king_x][$king_y] = null;
            
            //King is checked... Check if king is check mate
  

            //If every possible move for king is chess
            //then it's check mate
            $possible_moves = count($validmoves_king);
            error_log('possible moves of king: '."$possible_moves \r\n",3,'checks.log');

            $temp_gridpos[$x1][$y1] = null;            //Set current grid to null
            $temp_gridpos[$x2][$y2] = $active_piece;   //Set new grid to actual piece that was in current square
            
            $checked_gridpos = array();
            error_log("Valid move of kings at $king_x,$king_y " . print_r($validmoves_king,true) ."\r\n",3,'checks.log');

            foreach($validmoves_king as $vmk) {
                $king_moveto_x = $vmk[0];
                $king_moveto_y = $vmk[1];
                
                $temp_gridpos[$king_moveto_x][$king_moveto_y] = $king; 
                $temp_gridpos[$king_x][$king_y] = null;
                   
                for($yp=0;$yp<8;$yp++) {
                    for($xp=0;$xp<8;$xp++) {
                        $piece = $temp_gridpos[$xp][$yp];

                         if ($piece !== null && !$piece instanceof King && $game->get_whosturn() != $piece->get_color()) {
                            $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp);

                                if (!empty($validmoves_piece)) {
                                    
                                    foreach($validmoves_piece as $vmp) {
                                        if (isset($vmp[0]) && isset($vmp[1])) {
                                            $xgrid = $vmp[0];
                                            $ygrid = $vmp[1];  
                                            $cp = $temp_gridpos[$xgrid][$ygrid];
                                            

                                            if ($cp !== null) {
                                                if ($cp->get_color() != $game->get_whosturn()) {
                                                    break; //Same color. if some piece of same color is in it's way, just break out of this loop
                                                }  

                                                if ($cp instanceof King) {
                                                    
                                                    $checked_state = "yes";  
            
                                                    //ONLY VALID If checking on current kings position...                                  
                                                    //but check ONLY once for each gridpos!     
                                                    $check_already = false;
                                                    foreach($checked_gridpos as $cg) {
                                                        $xgrid = $cg[0];
                                                        $ygrid = $cg[1];
                                                        if ($xgrid == $king_moveto_x && $ygrid == $king_moveto_y) {
                                                            $check_already = true;
                                                        }
                                                    }
            
                                                    if ($check_already === false) {
                                                        $checked_gridpos[] = array($king_moveto_x, $king_moveto_y);
                                                        $possible_moves--;
                                                        error_log("decrease possible moves to $possible_moves \r\n",3,'checks.log');

                                                    }                                                    
                                                                                                        
                                                }

                                            }
                                        }
                                    }
                                }


                        }

                        
                    }
                }                
                
                $king_x = $king_moveto_x;
                $king_y = $king_moveto_y;
            }

        
            //Is it possible to remove the piece that is checking?
            error_log('Is it possible to remove the piece that is checking with any piece on the board?' . "\r\n",3,'checks.log');

            $attacker_can_be_removed = false;
        
            for($yp=0;$yp<8;$yp++) {
                for($xp=0;$xp<8;$xp++) {
                    $piece = $temp_gridpos[$xp][$yp];                        
                    if ($piece !== null && !$piece instanceof King && $game->get_whosturn() == $piece->get_color()) {
                        $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp);     
                        
                        if (!empty($validmoves_piece)) {
                            error_log('Checking at ' . $xp . ',' . $yp . "\r\n",3,'checks.log');
                            foreach($validmoves_piece as $vmk) {  
                                if (isset($vmk[0]) && isset($vmk[1])) { 
                                    $xk = $vmk[0];
                                    $yk = $vmk[1]; 
                                    
                                    error_log("IS $xk same as $x2 and $yk is same as $y2 ? \r\n",3,'checks.log');

                                    if ($xk == $x2 && $yk == $y2) {
                                        //If Pawn is attacking it must be diagonally
                                        //(If on the same column as it as from the start move
                                        //on the next iteration of this loop)
                                        if ($piece instanceof Pawn) {
                                            if ($xk == $xp) { //xk = x in valid moves, xp = current x of pawn
                                                continue;
                                            }
                                        }

                                        error_log('attacker can be removed (' . $xk . ',' . $yk . ')'."\r\n",3,'checks.log');
                                        $remove_piece = "yes";
                                        $attacker_can_be_removed = true;
                                        
                                    }
                                    
                                    //If pieces is "in the way" then 
                                    //break out of this loop
                                    if ($temp_gridpos[$xk][$yk] !== null) {                                        
                                        //break;
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if ($attacker_can_be_removed === false && $possible_moves == 0) {
                $checkmate = "yes";
                $checked_state = "yes";
                error_log('attacker can not be removed'."\r\n",3,'checks.log');

                //Possible moves are zero and attacker cannot be removed, but
                //is it possible to move piece (in front of king maybe) so attacker is not checking anymore?  
                $temp_gridpos = array_slice($orignal_gridpos,0,count($orignal_gridpos));

                $temp_gridpos[$x1][$y1] = null;            //Set current grid to null
                $temp_gridpos[$x2][$y2] = $active_piece;   //Set new grid to actual piece that was in current square
                

                //Get valid moves of the piece that is checking
                $checkerpiece = $temp_gridpos[$x2][$y2];
                $checkerpiece_validmoves = $checkerpiece->get_validmoves($temp_gridpos, $x2, $y2);
               
                foreach($checkerpiece_validmoves as $cpvm) {
                    if(!isset($cpvm[0]) && !isset($cpvm[1])) {
                        continue;
                    }

                    $x_cpvm = $cpvm[0];
                    $y_cpvm = $cpvm[1];
                    
                    //Go through all board for user that is checked (and see if 
                    //it's possible to move so it's not chess anymore
                    for($yp=0;$yp<8;$yp++) {
                        for($xp=0;$xp<8;$xp++) {
                            $piece = $temp_gridpos[$xp][$yp];
                            
                            if ($piece !== null && !$piece instanceof King && $piece->get_color() != $game->get_whosturn()) {
                                //Each valid move for this player                                    
                                $validmoves_piece = $piece->get_validmoves($temp_gridpos,$xp,$yp);     

                                //This tells that any piece of this user on board
                                //maybe can move to any square the checking piece can
                                if (!empty($validmoves_piece)) {                                       

                                    //Check in array (valid moves of current piece in loop)
                                    $can_move_to_dest = false;
                                    foreach($validmoves_piece as $vp) {

                                        if (!empty($vp[0]) && !empty($vp[1])) {
                                            if ($vp[0] == $x_cpvm && $vp[1] == $y_cpvm) {
                                                $can_move_to_dest = true;
                                                error_log("can move to destination $vp[0],$vp[1] \r\n",3,'checks.log');
                                                $checkmate = "no";
                                                break;
                                            }   
                                        }
                                    }

                                    if ($can_move_to_dest === true) {
                                        error_log('it is confirmed that user can move to same square the checker piece can'."\r\n",3,'checks.log');
                                        error_log('After that move , is it still chess?'."\r\n",3,'checks.log');
                                        error_log('if NO then its checkmate'."\r\n",3,'checks.log');


                                        //It's confirmed that user can move to same square the checker piece can
                                        //After that move, is it still chess? 
                                        //If NO, then it's NOT checkmate
                                        

                                        //Put current piece (in loop) temporarily at this position
                                        $temp_gridpos[$x_cpvm][$y_cpvm] = $piece; 

                                        if (!empty($validmoves_piece)) {
                                            $checkmate = "yes";
                                            
                                            $king_found = false;
                                            foreach($validmoves_piece as $vmp) {
                                                if (isset($vmp[0]) && isset($vmp[1])) {
                                                    $xgrid = $vmp[0];
                                                    $ygrid = $vmp[1];  
                                                    $cp = $temp_gridpos[$xgrid][$ygrid];
                                                    
        
                                                    if ($cp !== null) {
                                                        if ($cp->get_color() != $game->get_whosturn()) {
                                                            error_log('same color dont care'."\r\n",3,'checks.log');
                                                            break; //Same color. if some piece of same color is in it's way, just break out of this loop
                                                        }  
                                                    
                                                        //If still chess...?
                                                        if ($cp instanceof King) { 
                                                            $king_found = true;   
                                                            break;                                                                                                                                                                                                                                                                                 
                                                        }
        
                                                    }
                                                }
                                            }

                                            //...if still chess checkmate is true
                                            if ($king_found === true) {
                                                $checkmate = "no";
                                                $checked_state = "yes";
                                                break;
                                            }


                                        }

                                       
                                    }

                                    
                                
                                }

                            }
                            
                        }
                    }
                }


                
            }

        }


        if ($checked_state === "no") {            
            
            error_log("checked state is none, but check after the actual move");
           

            $found_checked = 0;
            for($yp=0;$yp<8;$yp++) {
                for($xp=0;$xp<8;$xp++) {
                    $piece = $temp_gridpos[$xp][$yp];
                    if ($piece !== null && !$piece instanceof King) { //IMPORTANT. DONT CHECK COLOR HERE!!!!
                        //Get valid moves for each piece on board and check
                        //if any piece is checking this king
                        $validmoves_piece = $piece->get_validmoves($temp_gridpos, $xp, $yp);
    
    
                        foreach($validmoves_piece as $vmp) {
                            if (isset($vmp[0]) && isset($vmp[1])) {
                                $xgrid = $vmp[0];
                                $ygrid = $vmp[1];  
                                $cp = $temp_gridpos[$xgrid][$ygrid];
                                
    
                                if ($cp !== null) {
                                    if ($cp->get_color() != $game->get_whosturn()) {
                                        error_log("same color $xp,$yp and $xgrid,$ygrid" ."\r\n",3,'checks.log');
                                        break; //Same color. if some piece of same color is in it's way, just break out of this loop
                                    }  
    
                                    if ($cp instanceof King) {
                                        error_log("LAST CHECK piece from $xp,$yp is checking $xgrid,$ygrid" ."\r\n",3,'checks.log');
                                        $checked_state = "yes";    
                                        $make_move ="no";                                   
                                    }
    
                                }
                            }
                        }
                        
                    }
                }
            }



        }


        $check_state = array();
        $check_state['move'] = array($x1,$y1,$x2,$y2);
        $check_state['checked'] = $checked_state;
        $check_state['checkmate'] = $checkmate;
        $check_state['makemove'] = $make_move;    
        $check_state['possibletoremovepiece'] = $remove_piece;      
         
        error_log('checked state='. print_r($check_state,true),3,'checks.log');
        return $check_state;

    }
    
    
    public function check_available_squares($gridpositions,$x,$y,$multi_x,$multi_y,$king_check = false,$check_other_players_color=true) {
        $king_check = true; //Include king in position
        $vm = array();
        $xd = $x + $multi_x;
        $yd = $y + $multi_y;

        if ($check_other_players_color === true) {
            $compare_color = $this->other_players_color;
        }
        else {
            $compare_color = $this->get_color();
        }
        
        $do_check = true;
        while($do_check === true) {   
            if ($xd>-1 && $xd<8 && $yd>-1 && $yd<8) {
                $check_piece = null;
                if (isset($gridpositions[$xd][$yd])) {
                    $check_piece = $gridpositions[$xd][$yd];
                }
           
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
                
                if ($check_piece !==null && $check_piece->get_color() === $compare_color && !$check_piece instanceof King) {
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
        $this->last_move = array($this->get_color(),$x,$y);
    }

    public function get_lastmove() {
        return $this->last_move;
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
