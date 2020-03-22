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
                    if ($piece !== null && $piece->get_color() != $this->turn) {
                        $this->inclusions[] = array($x,$y);
                    }
                }
            }
        }

    }

    public function get_randommove($attempt = 0) {        

            //shuffle($inclusions);

            //Randomize item from array inclusions
            //(because this array contains x,y-values with a piece and this turns color)
            //and fetch start position (x,y)
            $found_valid = false;
                        
            error_log("ATTEMPT $attempt - inclusions=" . print_r($this->inclusions,true) . "\r\n",3,'attempts.log');

            foreach ($this->inclusions as $inclusions_key=>$use_arritem) {

                $x1 = $use_arritem[0];
                $y1 = $use_arritem[1];

                //Random move (x,y) to (random from valid moves of selected piece)
                $selected_piece = $this->gp[$x1][$y1];
                $valid_moves = $selected_piece->get_validmoves($this->gp, $x1, $y1);
                if (!empty($valid_moves)) {                    
                    foreach($valid_moves as $use_validitem) {
                            if (isset($use_validitem[0]) && isset($use_validitem[1])) {
                                $x2 = $use_validitem[0];
                                $y2 = $use_validitem[1];    
                                      
                                error_log("TRY MOVE, x1:$x1, y1:$y1, x2:$x2, y2:$y2 \r\n",3,'attempts.log');

                                //Chess after move? (Is it not possible, then fetch new random move)
                                $is_chess = $selected_piece->check_chess($this->game, $this->gp, $selected_piece, $valid_moves, $x1, $y1, $x2, $y2);                            

                                $found_valid = true;
                                if ($is_chess['makemove'] === 'no') {
                                    error_log("makemove is no \r\n",3,'attempts.log');
                                    error_log("REMOVING!!! $inclusions_key \r\n",3,'attempts.log');
                                    unset($this->inclusions[$inclusions_key]);

                                    $found_valid = false;
                                }
                                
                                if ($found_valid === true) {
                                    error_log("makemove at $inclusions_key \r\n",3,'attempts.log');
                                    break;
                                }
                                
                            }

                            
                    }
                    
                }


               


               
            }

            
            $temp_game = serialize($this->game);


            if ($this->gp[$x1][$y1] !== null) {
                $game_obj = $this->game->move_to($x1,$y1,$x2,$y2,$this->turn); 
                $status = $game_obj->get_status();   
                  
            }
            else {
                $status = null;
            }

        

            $result = array();
            $result['board'] = $game_obj->draw();
            $result['turn'] = $game_obj->get_whosturn(); 
            $result['status'] = $status;
            $result['moved'] = array($x1, $y1, $x2, $y2);  

            if (strtolower(stristr($status, 'chess'))) {
                $this->game = unserialize($temp_game);
                $attempt++;
                return $this->get_randommove($attempt);
            }



            $_SESSION['game'] = serialize($game_obj);

            return $result;

    }

}