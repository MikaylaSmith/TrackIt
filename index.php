<?php
// Filename: index.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Main page, starting spot for the entire site

session_start();


if ($_GET['logout'] == 'yes') {
    session_destroy();
    header('Location: login.html');
}
elseif (isset($_SESSION['logged_in_user'])) {
    include('home.html');
}
elseif (isset($_POST['inputUsername'], $_POST['inputPassword'])) {
    require_once('db.php');

    $username = $_POST['inputUsername'];
    $password = $_POST['inputPassword'];

    //Get the user id to find the password
    $user_query = "SELECT * FROM users WHERE username = '".mysqli_real_escape_string($link, $username)."' LIMIT 1;";

    $user_result = mysqli_query($link, $user_query);

    if (mysqli_num_rows($user_result) == 1)
    {
        error_log("I have a username match");

        //If one result, keep going through stuff
        $user_info = mysqli_fetch_assoc($user_result);

        //Get the password that is stored in the database
        $password_query = "SELECT password FROM passwords_for_users WHERE user_id = '".$user_info['id']."';";

        $password_result = mysqli_query($link, $password_query);

        $password_queried = mysqli_fetch_assoc($password_result);

        error_log(var_export($password_queried, true));

        if ($password == $password_queried['password']){
          //Login information matches as it should, so log them in
          error_log("The passwords match");

          $_SESSION['logged_in_user'] = $user_info;

          error_log(var_export($_SESSION, true));

          include('home.html');
        }
        else {
          //If the passwords don't match
          error_log("Passwords don't match");

          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Passwords don\'t match records.'
          );
          include('login.html');
        }
    }
    else {
        //If there were no results, display error message
        error_log("No matching username");

        $_SESSION['message'] = Array('type' => 'danger',
            'text' => 'No username matches.'
        );
        include('login.html');
    }
}
elseif (!isset($_SESSION['logged_in_user'])) {
    include('login.html');
}



?>
