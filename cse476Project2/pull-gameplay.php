<?php
/**
 * Created by PhpStorm.
 * User: GregsMac
 * Date: 4/13/15
 * Time: 11:03 PM
 */

require_once "db.inc.php";

if(!isset($_GET['id']) || !isset($_GET['username'])) {
    echo '<birdGame status="no" msg="No ID or Username" />';
    exit;
}

$pdo = pdo_connect();
$id = $_GET['id'];
if(getPlayerTurn($pdo,$_GET['id']) == getId($pdo,$_GET['username'])){

    if(!isGameOver($pdo,$id)) {

        $sql = <<<SQL
SELECT gamexml FROM game
WHERE id = ?
SQL;

        $statement = $pdo->prepare($sql);
        $statement->execute(array($id));
        foreach ($statement as $row) {
            if ($row['gamexml'] == "") {
                echo '<?xml version="1.0" encoding="1.0" ?>';
                echo '<birdGame status="yes"/>';
                exit;
            }
            echo $row['gamexml'];
            exit;
        }
    }
}
else {

    if (!isGameOver($pdo, $id)) {

        echo '<?xml version="1.0" encoding="1.0" ?>';
        echo '<birdGame status="no"/>';
        exit;
    }
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

function isGameOver($pdo,$id){
    $sql =<<<SQL
SELECT over FROM game
WHERE id = ?
SQL;

    $statement = $pdo->prepare($sql);
    $statement->execute(array( $id));
    $result = $statement->fetch();
    if($result['over']==1){
        echo '<?xml version="1.0" encoding="1.0" ?>';
        echo '<birdGame status="Game Over"/>';
        return true;
    }
return false;
}