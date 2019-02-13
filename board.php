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
            if ($last_icolor == 0) {
                $icolor = 0;
            }
            else {
                $icolor = 1;
            }
            $html_board .= '<div style="clear:both;"></div>';
        }
       return $html_board;

    }
    

}
