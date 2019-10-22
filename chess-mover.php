<?php
session_start(); //Store game-object in session (serialize / unserialize when needed)
require_once 'game.php';
if (isset ($_POST['startover'])) {
    if ($_POST['startover']['reset'] == 1) {
        unset($_SESSION['game']);
    }
}
if (isset($_SESSION['game'])) {
    $game = unserialize($_SESSION['game']);
}
else {
    $game = new Game();
}

//These posted valeus comes from chess.js
//
//TODO!!! make it actually work :-)
if ( isset($_POST['movements'])) {  
    $moves = $_POST['movements'];
    echo json_encode($moves,true);
    //$result = $game->$board->move_piece($from, $to);
    //echo $result['html'];
    $_SESSION['game'] = serialize($game);
}
else {
    //$result = $board->draw_board();
    //echo $result;
}
