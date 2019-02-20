<?php
class Rook extends Piece {
    public $move_steps=1;

    public function get_validmoves($gridpositions, $x,$y,$x2,$y2,$check_other_players_color=true) {           
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0,false,$check_other_players_color);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0,false,$check_other_players_color);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1,false,$check_other_players_color);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1,false,$check_other_players_color);   //down                   
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);        
        return $valid_moves;
    }
    
    public function get_aftermove($gridpositions, $x,$y) {
        $valid_moves1 = $this->check_available_squares($gridpositions,$x,$y,-1,0,true);  //left
        $valid_moves2 = $this->check_available_squares($gridpositions,$x,$y,1,0,true);   //right        
        $valid_moves3 = $this->check_available_squares($gridpositions,$x,$y,0,-1,true);  //up     
        $valid_moves4 = $this->check_available_squares($gridpositions,$x,$y,0,1,true);   //down                   
        $valid_moves = array_merge($valid_moves1,$valid_moves2,$valid_moves3,$valid_moves4);

        $return_str = 'Rook moved ';
        $chess = $this->check_chess($gridpositions,$valid_moves);
        
        $chess_arr = null;
        if ($chess !== false) {
            $return_str .= ':chess (' . $chess[0] . '-' . $chess[1] .')';
            $chess_arr = array_slice($valid_moves,0,count($valid_moves));
        }
        
         return array($gridpositions,$return_str,$chess_arr);
    }    
    
    //Get chess character
    public function get_char() {
        if ($this->color == 0) {
            return '&#9820';
        }
        return '&#9814';
    }    
}
