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


            //Randomize item from array inclusions
            //(because this array contains x,y-values with a piece and this turns color)
            //and fetch start position (x,y)

            tryagain:

            $already_included_key = array();
            $found_valid = false;
            while ($found_valid === false) {
                
                $use_key = false;
                while ($use_key === false) {
                    $inclusions_key = array_rand($inclusions, 1); //1 = return only one index from array

                    if (in_array($inclusions_key, $already_included_key, true) !== true) {
                        $use_key = true;
                        $already_included_key[] = $inclusions_key;
                        break;
                    }
                }

                //Is number of pieces done?
                //If so then this is checkmate
                if (count($already_included_key) == count($inclusions)) {
                    $result = array();
                    $result['board'] = $game_obj->draw();
                    $result['turn'] = $this->turn; 
                    $result['status'] = "CHECK MATE!!!";
                    $result['moved'] = array($x1, $y1, $x2, $y2);                    
                    return $result;        
                }

                $use_arritem = $inclusions[$inclusions_key];
                $x1 = $use_arritem[0];
                $y1 = $use_arritem[1];

                //Random move (x,y) to (random from valid moves of selected piece)
                $selected_piece = $this->gp[$x1][$y1];
                $valid_moves = $selected_piece->get_validmoves($this->gp, $x1, $y1, $x1, $y1);
                if (!empty($valid_moves)) {                    
                    $valid_key = array_rand($valid_moves, 1);
                    if ($valid_key != null) {
                        $use_validitem = $valid_moves[$valid_key];                        
                        if (isset($use_validitem[0]) && isset($use_validitem[1])) {
                            $x2 = $use_validitem[0];
                            $y2 = $use_validitem[1];    
                            $found_valid = true;
                        }
                    }
                    
                }

            }

            $game_obj = $this->game->move_to($x1,$y1,$x2,$y2,$this->turn);

            $status = $game_obj->get_status();
            if ($status == 'redo') {
                goto tryagain;
            }

            $_SESSION['game'] = serialize($game_obj);
            $result = array();
            $result['board'] = $game_obj->draw();
            $result['turn'] = $game_obj->get_whosturn(); 
            $result['status'] = $status;
            $result['moved'] = array($x1, $y1, $x2, $y2);            
            return $result;

    }

}