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
    
    //Renew chessboard and piece locations
    //based on new given gridpos array
    public function renew($gridpos) {
        $this->grid_positions = array_slice($gridpos, 0, count($gridpos));        
    }
    
    //Init of chessboard and location of pieces
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

    //Create the actual chessboard with chesspieces
    //based on gridpositions array
    public function output_html($grid_positions = null) {
        if ($grid_positions === null) {
            $grid_positions = array_slice($this->grid_positions,0,count($this->grid_positions));        
        }

        $html_board = '';
        $bgcolor = array('black','white');
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
                    $html_board .= '<div data-x="' . $x . '" data-y="' . $y . '" class="square ' . $col . '">';
                    $square_content = $grid_positions[$x][$y];
                    if ( $square_content !== null ) {
                        $html_board .= $grid_positions[$x][$y]->get_char();
                    }
                    else {
                        $html_board .= '&nbsp;';
                    }
                    $html_board .= '</div>';
                    $index++;
            }
            $icolor = $last_icolor;
            $html_board .= '<div class="clearfix"></div>';
        }      
        return $html_board;
    }
}