<?php
require_once "db.inc.php";
echo '<?xml version="1.0" encoding="UTF-8" ?>';

// Ensure the xml post item exists
if(!isset($_POST['xml'])) {
    echo '<birdGame status="no" msg="missing XML" />';
    exit;
}

processXml(stripslashes($_POST['xml']));

/**
 * Process the XML query
 * @param $xmltext the provided XML
 */
function processXml($xmltext) {
    // Load the XML
    $xml = new XMLReader();
    if(!$xml->XML($xmltext)) {
        echo '<birdGame status="no" msg="invalid XML" />';
        exit;
    }

    // Connect to the database
    $pdo = pdo_connect();

    // Read to the start tag
    while($xml->read()) {
        if ($xml->nodeType == XMLReader::ELEMENT && $xml->name == "birdGame") {
            // We have the bird tag
            $magic = $xml->getAttribute("magic");
            if ($magic != "NechAtHa6RuzeR8x") {
                echo '<birdGame status="no" msg="magic" />';
                exit;
            }

            while ($xml->read()){
                if($xml->nodeType == XMLReader::ELEMENT && $xml->name == "newuser"){
                    $username = $xml->getAttribute("username");
                    $password = $xml->getAttribute("password");
                    if(newUser($pdo, $username, $password)){
                        echo '<birdGame status="yes"/>';
                        exit;
                    }
                    echo '<birdGame status="no" msg="Username Already Exists">';
                    exit;
                }

            }
        }
    }
    echo '<birdGame save="no" msg="invalid XML" />';

}

/**
 * Ask the database for the user ID. If the user exists, the password
 * must match.
 * @param $pdo PHP Data Object
 * @param $user The user name
 * @param $password Password
 * @return id if successful or exits if not
 */
function newUser($pdo, $username, $password) {

    if(!userExists($pdo,$username)) {
        $sql =<<<SQL
INSERT INTO birdUser(username,password)
VALUES(?,?)
SQL;
        $statement = $pdo->prepare($sql);

        $statement->execute(array($username,$password));
        if($statement->rowCount() === 0) {
            return null;
        }
        return true;
    }
    return false;
}

function userExists($pdo, $username){
    // Does the user exist in the database?
    $userQ = $pdo->quote($username);
    $query = "SELECT id from birdUser where username=$userQ";

    $rows = $pdo->query($query);
    if(count($rows) != 0) {
        return false;
    }
    return true;
}