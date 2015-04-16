<?php
/**
 * Created by PhpStorm.
 * User: GregsMac
 * Date: 4/13/15
 * Time: 6:56 PM
 */

require_once "db.inc.php";
echo '<?xml version="1.0" encoding="1.0" ?>';

if(!isset($_GET['magic']) || $_GET['magic'] != "NechAtHa6RuzeR8x") {
    echo '<login status="no" msg="magic" />';
    exit;
}

if(!isset($_GET['username']) || !isset($_GET['password'])) {
    echo '<birdGame status="no" msg="Username or Password Missing" />';
    exit;
}

$username = $_GET['username'];


$pdo = pdo_connect();

endGame($pdo,$username);

function endGame($pdo,$username){
    $userQ = $pdo->quote($username);
    $id = getGameId($pdo, $username);

$sql1 =<<<SQL
UPDATE game
SET gamexml = ""
SET player1 = 0
SET player2 = 0
WHERE id = ?
SQL;

    $statement = $pdo->prepare($sql1);

    $statement->execute(array($id));

}

function getId($pdo,$username){
    $userQ = $pdo->quote($username);
    $query = "SELECT id from birdUser where username=$userQ";

    $rows = $pdo->query($query);
    if($row = $rows->fetch()) {
        return $row['id'];
    }

}

function getGameId($pdo, $userid){
    $sql =<<<SQL
SELECT * FROM game
WHERE player1 = ? or player2 = ?
SQL;
    $statement = $pdo->prepare($sql);

    $statement->execute(array($userid, $userid));
    foreach($statement as $row) {
        return $row['id'];
    }
}