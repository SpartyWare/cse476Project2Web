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

if(!isset($_GET['username'])) {
    echo '<birdGame status="no" msg="Username Missing" />';
    exit;
}
if(!isset($_GET['id'])){
    echo '<birdGame status="no" msg="Id Missing" />';
    exit;
}

$username = $_GET['username'];

$pdo = pdo_connect();

if($_GET['id']==-1){
    makeUserLoggedOut($pdo,$username);
}
endGame($pdo,$username);

function endGame($pdo,$username){
    //$id = getGameId($pdo, $username);
    $gameId = $_GET['id'];

$sql1 =<<<SQL
UPDATE game
SET over = 1
WHERE id = ?
SQL;

    $statement = $pdo->prepare($sql1);

    $statement->execute(array($gameId));

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
SELECT *
FROM game
WHERE player1 =?
OR player2 =?
ORDER BY id DESC
SQL;
    $statement = $pdo->prepare($sql);

    $statement->execute(array($userid, $userid));
    foreach($statement as $row) {
        return $row['id'];
    }
}


function makeUserLoggedOut($pdo,$username){
    $sql1 =<<<SQL
UPDATE birdUser
SET status = 0
WHERE username = ?
SQL;

    $statement = $pdo->prepare($sql1);

    $statement->execute(array($username));
}