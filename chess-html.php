<?php
session_start(); //Store board-object in session (serialize / unserialize when needed)
require_once('piece.php');
require_once('pawn.php');
require_once('rook.php');
require_once('knight.php');
require_once('bishop.php');
require_once('queen.php');
require_once('king.php');
require_once('passant.php');
require_once('board.php');
if (isset ($_POST['startover'])) {
    if ($_POST['startover']['reset'] == 1) {
        unset($_SESSION['board']);
    }
}
if (isset($_SESSION['board'])) {
    $board = unserialize($_SESSION['board']);
}
else {
    $board = new Board();
}

if ( isset($_POST['movements'])) {  
    $from = $_POST['movements']['from'];
    $to = $_POST['movements']['to'];
    $result = $board->move_piece($from, $to);
    echo $result['html'];
    $_SESSION['board'] = serialize($board);
}
else {
    $board->game_start();
    echo $board->output_html();
}
