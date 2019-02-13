<?php
session_start(); //Store board-object in session (serialize / unserialize when needed)
require_once 'board.php';
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
    $result = $board->draw_board();
    echo $result;
}
