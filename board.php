<?php
class Board {
    private $grid_positions = array();
    
    public function get_gridpositions() {
        return $this->grid_positions;
    }    
    
    public function get_piece($x,$y) {
        $gp = $this->get_gridpositions();
        return $gp[$x][$y];     
    }    
    
    public function renew($gridpos) {
        $this->grid_positions = array_slice($gridpos, 0, count($gridpos));        
    }
    
    public function game_start() {  
        for($y=0;$y<8;$y++) {
            for($x=0;$x<8;$x++) {            
                $this->grid_positions[$x][$y] = null;
            }
        }
        
        for($x=0;$x<8;$x++) {  
            $this->grid_positions[$x][1] = new Pawn(0,1); 
            $this->grid_positions[$x][6] = new Pawn(1,-1);
        }
        $this->grid_positions[0][0] = new Rook(0);
        $this->grid_positions[7][0] = new Rook(0);
        $this->grid_positions[0][7] = new Rook(1);
        $this->grid_positions[7][7] = new Rook(1);
        $this->grid_positions[1][0] = new Knight(0);
        $this->grid_positions[6][0] = new Knight(0);
        $this->grid_positions[1][7] = new Knight(1);
        $this->grid_positions[6][7] = new Knight(1); 
        $this->grid_positions[2][0] = new Bishop(0);
        $this->grid_positions[5][0] = new Bishop(0);
        $this->grid_positions[2][7] = new Bishop(1);
        $this->grid_positions[5][7] = new Bishop(1);     
        $this->grid_positions[3][0] = new Queen(0);
        $this->grid_positions[3][7] = new Queen(1);
        $this->grid_positions[4][0] = new King(0);
        $this->grid_positions[4][7] = new King(1);      
    }
    
    public function move_to($x1,$y1,$x2,$y2) {
        $make_move = false;
        echo 'x1=' . $x1 . ', y1=' . $y1;
         echo 'TO x2=' . $x2 . ', y2=' . $y2;
        $this->gridpos = $this->boardobj->get_gridpositions();
        $active_piece = $this->get_piece($x1,$y1);   
        $valid_moves = $active_piece->get_validmoves($this->gridpos,$x1,$y1,$x2,$y2);                
        echo '<pre>';
        var_dump ($valid_moves);
        var_dump($active_piece);
        echo '</pre>';

        
        //Make sure player only are able to go to valid locations
        foreach($valid_moves as $vm) {
            $check_movetox = $vm[0];
            $check_movetoy = $vm[1];
            if (($check_movetox == $x2) && ($check_movetoy == $y2)) {
                $make_move = true;  
                break;
            }
        }
        
        if ($make_move == false) {
            echo '<h2>Invalid move. Nothing happens on board!</h2>';
            $this->draw();
            return;
        }        

        $this->gridpos[$x1][$y1] = null;
        $this->gridpos[$x2][$y2] = $active_piece;
        
        $after_move = $active_piece->get_aftermove($this->gridpos,$x2,$y2);
        echo '<b>' .$after_move[1] .'</b>';    
        
        //Regenerate gridpos (after move)
        $this->gridpos = array_slice($after_move[0],0,count($after_move[0]));
        
        if ($active_piece->get_waituser() === false) {
            $active_piece->last_move($x2,$y2);
            $active_piece->not_first_move();
            $this->boardobj->renew($this->gridpos);
            $this->draw();
        }        
        
    }

    public function output_html() {
        $html_board = '';
        $bgcolor = array('#cccccc','#FFFFFF');
        $icolor = 1;
        $index=0;
        for($y=0;$y<8;$y++) {            
            for($x=0;$x<8;$x++) {
                    $col = $bgcolor[$icolor];
                    $last_icolor = $icolor;
                    $icolor++;
                    if ($icolor>1) {
                        $icolor = 0;
                    }
<<<<<<< HEAD
                    $html_board .= '<div class="chess-square" id="chessindex-' . $index . '" style="text-align:center;width:100px;height:100px;font-size:64px;float:left;background:' . $col . '">';
=======
                    $html_board .= '<div data-x="' . $x . '" data-y="' . $y . '" class="square" id="chessindex-' . $index . '" style="text-align:center;width:100px;height:100px;font-size:64px;float:left;background:' . $col . '">';
>>>>>>> 44fcc9e9b09e5996a8054c695b27ab0c5a1925a3
                    $square_content = $this->grid_positions[$x][$y];
                    if ( $square_content !== null ) {
                        $html_board .= $this->grid_positions[$x][$y]->get_char();
                    }
                    else {
                        $html_board .= '&nbsp;';
                    }
                    $html_board .= '</div>';
                    $index++;
            }
            $icolor = $last_icolor;
            $html_board .= '<div style="clear:both;"></div>';
        }      
       return $html_board;

    }
    

}
