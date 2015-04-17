<?php
/**
 * Created by PhpStorm.
 * User: GregsMac
 * Date: 4/13/15
 * Time: 8:11 PM
 */

require_once "db.inc.php";
echo '<?xml version="1.0" encoding="1.0" ?>';

if(!isset($_POST['xml'])) {
    echo '<birdGame status="no" msg="No XML " />';
    exit;
}

if(!isset($_GET['id']) || !isset($_GET['username'])) {
    echo '<birdGame status="no" msg="No ID or Username " />';
    exit;
}

$pdo = pdo_connect();
$xml = $_POST['xml'];
$id = $_GET['id'];
$userId = getId($pdo,$_GET['username']);
$magic = $_GET['magic'];

playerMadeMove($pdo,$xml,$id,$userId);
echo '<birdGame status="yes"/>';
exit;

function playerMadeMove($pdo,$xml,$id,$userid){
    $playersTurn = changeTurn($pdo,$userid,$id);
    if($playersTurn!= null) {
        $sql = <<<SQL
UPDATE game
SET gamexml = ?, turn = ?
WHERE id = ?
SQL;

        $statement = $pdo->prepare($sql);
        $statement->execute(array($xml, $playersTurn, $id));
    }

}

function changeTurn($pdo,$userid,$id){
    //Get Placed count
    $placedCount = getPlacedCount($pdo,$id);

    if($placedCount%2 ==0){
        if($placedCount==0){ incrementPlacedCount($pdo,$id);}
        $playersTurn = "";
        $sql =<<<SQL
SELECT player1,player2,turn FROM game
WHERE id = ?
SQL;

        $statement = $pdo->prepare($sql);
        $statement->execute(array($id));
        foreach($statement as $row) {
            if($row['turn']==$row['player1']){
                $playersTurn=$row['player2'];
            }
            else{
                $playersTurn=$row['player1'];
            }
        }
        if($placedCount!=0){incrementPlacedCount($pdo,$id);}
        return $playersTurn;
    }
    incrementPlacedCount($pdo,$id);
    return null;



}

function getPlayerTurn($pdo,$id){
    $sql =<<<SQL
SELECT turn FROM game
WHERE id = ?
SQL;
    $statement = $pdo->prepare($sql);

    $statement->execute(array($id));
    foreach($statement as $row) {
        return $row['turn'];
    }
}




function getId($pdo,$username){
    $userQ = $pdo->quote($username);
    $query = "SELECT id from birdUser where username=$userQ";

    $rows = $pdo->query($query);
    if($row = $rows->fetch()) {
        return $row['id'];
    }

}

function incrementPlacedCount($pdo,$id){
    $sql =<<<SQL
UPDATE game
SET placedCount = placedCount + 1
WHERE id = ?
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute(array($id));
}

function getPlacedCount($pdo, $id){
    $sql =<<<SQL
SELECT * FROM game
WHERE id = ?
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute(array($id));
    $result = $statement->fetch();
    return $result['placedCount'];
}