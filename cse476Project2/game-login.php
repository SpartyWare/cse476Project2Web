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



/*$user = $_GET['username'];
$length = strlen($user);
var_dump($length);
$password = $_GET['password'];
echo <<<XML
<tag user="$user" pw="$password" />
XML;
*/

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
        echo '<birdGame status ="yes"/>';
        return $row;
    }
    echo '<birdGame status="no" msg="user error" />';
    exit;
}