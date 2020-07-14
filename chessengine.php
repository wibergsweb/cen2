<?php
class Chessengine {
    private $gp;
    private $turn;
    private $game;
    private $inclusions = array();

    public function __construct(Game $game, array $gridpositions, $turn) {
        $this->gp = $gridpositions;
        $this->turn = $turn;
        $this->game = $game;

        //Which squares do contain a piece
        //that has the color of current player?     
        for($y=0;$y<8;$y++) {
            for($x=0;$x<8;$x++) {
                if (isset($this->gp[$x][$y])) {
                    $piece = $this->gp[$x][$y];
                    //Make sure only select pieces of current color
                    if ($piece !== null && ($piece->get_color() != $this->turn)) {
                        $this->inclusions[] = array($x,$y);
                    }
                }
            }
        }
    }

    public function get_randommove($attempt = 0) {        
            //Create an array with all valid moves both FROM position and TO position
            $all_validmoves = [];
            $in_chess_aftermove = false;
            foreach ($this->inclusions as $inclusions_key=>$use_arritem) {               

                $x1 = $use_arritem[0];
                $y1 = $use_arritem[1];

                //Random move (x,y) to (random from valid moves of selected piece)
                $selected_piece = $this->gp[$x1][$y1];

                $valid_moves = $selected_piece->get_validmoves($this->gp, $x1, $y1);       

                if (!empty($valid_moves) && !empty($valid_moves[0]) ) {      
                    foreach($valid_moves as $use_validitem) {
                        error_log("CHECKING ITEM" . print_r($use_validitem,true) . "\r\n",3,'attempts.log');

                            $x2 = $use_validitem[0];
                            $y2 = $use_validitem[1];  
       
                            //Get status after computer's move
                            $fake_gridpositions = array_slice($this->gp,0,count($this->gp));
                            $fake_gridpositions[$x1][$y1] = null;            
                            $fake_gridpositions[$x2][$y2] = $selected_piece;                            
                            $after_move = $selected_piece->get_aftermove($fake_gridpositions,$x2,$y2);
                            $am = $after_move[1];

  

                            if (stristr($am,'chess') !== false && get_class($selected_piece) != 'King') {
                                //If computer is in chess after computermove
                                $in_chess_aftermove = true;
                            }
                            else {
                                //If computer is NOT in chess after computermove, then add availability
                                $all_validmoves[] = [$x1,$y1,$x2,$y2, $after_move[1]];
                            }
                    }
                    

                }
            }

            error_log("all valid moves BEFORE= " . print_r($all_validmoves,true) . "\r\n",3,'attempts.log');

            //User has put computer in chess. 
            //Computer king must move out from it somehow
            if ($in_chess_aftermove === true) {
                $new_validmoves = [];

                //Find all valid moves for king
                foreach($all_validmoves as $key=>$vm) {
                    if ( strstr($vm[4], 'King') !== false  ) {
                        $new_validmoves[] = $vm;                            
                    }             
                }

                //If no valid moves for king found, pick another piece that does not make computer
                //be in chess
                if (!empty($new_validmoves)) {
                    $all_validmoves = array_slice( $new_validmoves,0,count($new_validmoves) );
                }
            }

            error_log("all valid moves AFTER= " . print_r($all_validmoves,true) . "\r\n",3,'attempts.log');

            //Select one from all valid moves
            if (count($all_validmoves)>1) {
                $r = array_rand($all_validmoves);
            }
            else {
                $r = 0;
            }

            $x1 = $all_validmoves[$r][0];
            $y1 = $all_validmoves[$r][1];
            $x2 = $all_validmoves[$r][2];
            $y2 = $all_validmoves[$r][3];
            
            $spec = 'R=' . $r . "\r\n";
            $spec .= 'x1=' . $x1 .' , y1=' . $y1 . "\r\n";
            $spec .= 'Y2=' . $y1 .' , y2=' . $y2 . "\r\n";

            error_log("actual move= " . print_r($spec,true) . "\r\n",3,'attempts.log');
            //and do the actual move
            $game_obj = $this->game->move_to($x1,$y1,$x2,$y2,$this->turn); 
            $status = $game_obj->get_status();

            $result = array();
            $result['board'] = $game_obj->draw();
            $result['turn'] = $game_obj->get_whosturn(); 
            $result['status'] = $status;
            $result['moved'] = array($x1, $y1, $x2, $y2);  


            $_SESSION['game'] = serialize($game_obj);

            return $result;

    }

}