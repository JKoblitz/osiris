<?php
function login($username, $password)
{
    global $osiris;
    $return = array("msg" => '', "success" => false);

    $USER = $osiris->persons->findOne([
        'username'=> $username,
        'password'=> $password
    ]);

    if (empty($USER)){
        $return["msg"] = "Login failed or user not found.";
    } else {
        $_SESSION['username'] = $username;
        $_SESSION['loggedin'] = true;
    
        $return["status"] = true;
    }
    
    return $return;
};