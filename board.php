<?php

class Board {
    private $grid_positions = array();
    public function new_game() {
  
        for($y=0;$y<8;$y++) {
            for($x=0;$x<8;$x++) {            
                $this->grid_positions[$x][$y] = 0;
            }
        }

        $black_pawn = new Pawn(0);  
        $white_pawn = new Pawn(1);
        for($x=0;$x<8;$x++) {            
            $this->grid_positions[$x][1] = $black_pawn;
            $this->grid_positions[$x][6] = $white_pawn;
        }
        $black_rook = new Rook(0);
        $white_rook = new Rook(1);
        $this->grid_positions[0][0] = $black_rook;
        $this->grid_positions[7][0] = $black_rook;
        $this->grid_positions[0][7] = $white_rook;
        $this->grid_positions[7][7] = $white_rook;
        $black_knight = new Knight(0);
        $white_knight = new Knight(1);
        $this->grid_positions[1][0] = $black_knight;
        $this->grid_positions[6][0] = $black_knight;
        $this->grid_positions[1][7] = $white_knight;
        $this->grid_positions[6][7] = $white_knight;   
        $black_bishop = new Bishop(0);
        $white_bishop = new Bishop(1);
        $this->grid_positions[2][0] = $black_bishop;
        $this->grid_positions[5][0] = $black_bishop;
        $this->grid_positions[2][7] = $white_bishop;
        $this->grid_positions[5][7] = $white_bishop;         
        $black_queen = new Queen(0);
        $white_queen = new Queen(1);
        $this->grid_positions[3][0] = $black_queen;
        $this->grid_positions[3][7] = $white_queen;  
        $black_king = new King(0);
        $white_king = new King(1);
        $this->grid_positions[4][0] = $black_king;
        $this->grid_positions[4][7] = $white_king;         
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
                    $html_board .= '<div id="chessindex-' . $index . '" style="text-align:center;width:100px;height:100px;font-size:64px;float:left;background:' . $col . '">';
                    $square_content = $this->grid_positions[$x][$y];
                    if ( $square_content !== 0 ) {
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
