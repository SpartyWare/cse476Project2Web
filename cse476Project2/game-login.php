<?php
/**
 * Created by PhpStorm.
 * User: SpartyWare
 * Date: 4/4/15
 * Time: 3:38 PM
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

$pdo = pdo_connect();

$user = getUser($pdo, $_GET['username'], $_GET['password']);

/**
 * Ask the database for the user ID. If the user exists, the password
 * must match.
 * @param $pdo PHP Data Object
 * @param $user The user name
 * @param $password Password
 * @return id if successful or exits if not
 */
function getUser($pdo, $user, $password) {
    // Does the user exist in the database?
    $userQ = $pdo->quote($user);
    $query = "SELECT id,username,password from birdUser where username=$userQ";

    $rows = $pdo->query($query);
    if($row = $rows->fetch()) {
        // We found the record in the database
        // Check the password
        if($row['password'] != $password) {
            echo '<birdGame status="no" msg="password error" />';
            exit;
        }
        if(waiting($pdo,$user,$password)){
            $userid = getId($pdo,$user);
            $gameId = getGameId($pdo,$userid);
            echo '<birdGame status ="yes" userstatus="0" gameid="'.$gameId.'"/>';
            exit;
        }
        echo '<birdGame status ="yes" userstatus="1"/>';
        return $row;
    }
    echo '<birdGame status="no" msg="user error" />';
    exit;
}

function waiting($pdo, $username, $password){
    //Is there anyone else waiting to be matched.
    $sql =<<<SQL
SELECT * FROM birdUser
WHERE status = 1
SQL;
    $statement = $pdo->prepare($sql);

    $statement->execute();
    if($statement->rowCount() === 0) {
        playerWaiting($pdo, $username, $password);
        return false;
    }
    foreach($statement as $row){
        if($row['username'] != $username){
            matched($pdo, getId($pdo,$username), getId($pdo,$row['username']));
            return true;
        }
    }
    return false;
}

function matched($pdo, $id1, $id2){
    $sql =<<<SQL
INSERT INTO game(player1,player2,over,gamexml,turn)
VALUES(?,?,?,?,?)
SQL;
    $statement = $pdo->prepare($sql);
    $statement->execute(array($id1,$id2,false,"",$id1));
    unsetPlayerStatus($pdo,$id1,$id2);

}


function getId($pdo,$username){
    $userQ = $pdo->quote($username);
    $query = "SELECT id from birdUser where username=$userQ";

    $rows = $pdo->query($query);
    if($row = $rows->fetch()) {
        return $row['id'];
    }

}

function playerWaiting($pdo,$username,$password){
    $sql =<<<SQL
UPDATE  birdUser
SET status = 1
WHERE username = ?
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute(array($username));
}

function unsetPlayerStatus($pdo,$id1,$id2){
    $sql =<<<SQL
UPDATE birdUser
SET status = 0
WHERE id = ? or id = ?
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute(array($id1, $id2));
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