<?php
class King extends Piece {
      //private bool color (0=black,1=white)
    //private bool first_move (true/false)
    //private bool final_row (true/false)

    //move_pattern (forward=1, backward=0, left=0, right=0)
    //threat() → change move_pattern  (forward=1, backward=0, left=1, right=1)
    //firstmove() → change move_pattern (forward = 1 or 2, backward=0, left=0, right=0)
    //range = 1 //how many steps
    //available_moves(); //based on move_pattern() and range
}
