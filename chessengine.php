<?php
class Chessengine {
    private $gp;
    private $turn;
    private $game;

    public function __construct(Game $game, array $gridpositions, $turn) {
        $this->gp = $gridpositions;
        $this->turn = $turn;
        $this->game = $game;
    }

    public function get_randommove() {        

            //Which squares do contain a piece
            //that has the color of current player?
            $inclusions = array();
            for($y=0;$y<8;$y++) {
                for($x=0;$x<8;$x++) {
                    if (isset($this->gp[$x][$y])) {
                        $piece = $this->gp[$x][$y];
                        //Make sure only select pieces of current color
                        if ($piece !== null && $piece->get_color() != $this->turn) {
                            $inclusions[] = array($x,$y);
                        }
                    }
                }
            }

            shuffle($inclusions);

            //Randomize item from array inclusions
            //(because this array contains x,y-values with a piece and this turns color)
            //and fetch start position (x,y)
            $found_valid = false;
                        
            error_log("inclusions=" . print_r($inclusions,true) . "\r\n",3,'attempts.log');

            foreach ($inclusions as $inclusions_key=>$use_arritem) {

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
                                    $found_valid = false;
                                }
                                
                                if ($found_valid === true) {
                                    error_log("makemove at $inclusions_key \r\n",3,'attempts.log');
                                    break;
                                }
                                
                            }
                    }
                    
                }

                if ($found_valid === true) {
                    break;
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

            if (strtolower(stristr($result['status'],'chess'))) {
                $result['status'] = "chess - wrong move";   
                error_log("STILL CHESS AT " . print_r($result, true) . "\r\n",3,'attempts.log');

                $game_obj = unserialize($temp_game);
                $result['board'] = $game_obj->draw();

                //Move back                      
                $this->get_randommove();
            } 
            else {
                error_log("STATUS= " . print_r($result, true) . "\r\n",3,'attempts.log');
            }


                        
            //If no valid moves found for computer, then it must be checkmate
            if ($found_valid === false) {
                $status = 'CHECK MATE!!!';
            }


            $_SESSION['game'] = serialize($game_obj);

            return $result;

    }

}