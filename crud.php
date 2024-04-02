<?php
// Filename: crud.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Create and edit informatoin for
// Education Logs, Students, Journal Entries, Budget Logs
session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

//Actions that can be done for logged in users
if ($_SESSION['logged_in_user']['id'] > 0) {
    require_once('db.php');

    if($_SESSION['logged_in_user']['studentAccess'] == 'true' &&
                            ( isset($_POST['educationCreate']) || isset($_POST['studentCreate'])
                            || isset($_POST['educationEdit']) || isset($_POST['studentEdit']) )) {
        //Do queries for education log and student related actions

        //Insert
        if (isset($_POST['educationCreate']))
        {
          $query = 'INSERT INTO educationLogs (user_id, student_id, log_date, school, grade, session_time, notes)
                    VALUES (?, ?, ?, ?, ?, ?, ?);';

          $stmt = $link->prepare($query);

          $stmt->bind_param("iissiis",
                        $_SESSION['logged_in_user']['id'],
                        $_POST['student'],
                        $_POST['date'],
                        $_POST['school'],
                        $_POST['grade'],
                        $_POST['minutes'],
                        $_POST['note']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error adding new education log.'
            );
          }
          else {
            //If no errors, display success message
            $_SESSION['message'] = Array('type' => 'success',
                'text' => 'Added new education log.'
            );
          }

          header('Location: logs.php');
        }
        else if (isset($_POST['studentCreate']))
        {
          $query = 'INSERT INTO students (user_id, student_name, school, grade)
                    VALUES (?, ?, ?, ?);';

          $stmt = $link->prepare($query);

          $stmt->bind_param("issi",
                        $_SESSION['logged_in_user']['id'],
                        $_POST['name'],
                        $_POST['school'],
                        $_POST['grade'],

                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error adding new student.'
            );
          }
          else {
            //If no errors, display success message
            $_SESSION['message'] = Array('type' => 'success',
                'text' => 'Added new student.'
            );
          }

          header('Location: students.php');
        }

        //Update
        else if (isset($_POST['educationEdit']))
        {
          $query = 'UPDATE educationLogs SET user_id = ? , student_id = ?, log_date = ?, school = ?, grade = ?, session_time = ?, notes = ? WHERE log_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("iissiisi",
                        $_SESSION['logged_in_user']['id'],
                        $_POST['student'],
                        $_POST['date'],
                        $_POST['school'],
                        $_POST['grade'],
                        $_POST['minutes'],
                        $_POST['note'],
                        $_POST['log_id_to_edit']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error editing education log.'
            );
          }
          else {
            //If no errors, display success message
            $_SESSION['message'] = Array('type' => 'success',
                'text' => 'Successfully edited education log.'
            );
          }

          header('Location: logs.php');
        }
        else if (isset($_POST['studentEdit']))
        {
          $query = 'UPDATE students SET user_id = ?, student_name = ?, school = ?, grade = ? WHERE student_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("issii",
                        $_SESSION['logged_in_user']['id'],
                        $_POST['name'],
                        $_POST['school'],
                        $_POST['grade'],
                        $_POST['student_id_to_edit']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error editing student.'
            );
          }
          else {
            //If no errors, display success message
            $_SESSION['message'] = Array('type' => 'success',
                'text' => 'Successfully edited student.'
            );
          }

          header('Location: students.php');
        }
        else {
          //If something doesn't go right, send to home
          header('Location: home.html');
        }

    }
    else if ($_SESSION['logged_in_user']['journalAccess'] == 'true' && ( isset($_POST['journalCreate']) || isset($_POST['journalEdit']) ) ){
      //Do queries for journal entry related actions

      //Insert
      if (isset($_POST['journalCreate']))
      {
        $query = 'INSERT INTO journalLogs (user_id, title, notes)
                  VALUES (?, ?, ?);';

        $stmt = $link->prepare($query);

        $stmt->bind_param("iss",
                      $_SESSION['logged_in_user']['id'],
                      $_POST['title'],
                      $_POST['note']
                    );
        if (!$stmt->execute())
        {
          //If there was an error with executing, display an error
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error adding new journal entry.'
          );
        }
        else {
          //If no errors, display success message
          $_SESSION['message'] = Array('type' => 'success',
              'text' => 'Added new journal entry.'
          );
        }

        header('Location: journals.php');
      }

      //Update
      else if (isset($_POST['journalEdit']))
      {
        $query = 'UPDATE journalLogs SET user_id = ? , title = ?, notes = ? WHERE log_id = ?;';

        $stmt = $link->prepare($query);

        $stmt->bind_param("issi",
                      $_SESSION['logged_in_user']['id'],
                      $_POST['title'],
                      $_POST['note'],
                      $_POST['journal_id_to_edit']
                    );
        if (!$stmt->execute())
        {
          //If there was an error with executing, display an error
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error editing journal entry.'
          );
        }
        else {
          //If no errors, display success message
          $_SESSION['message'] = Array('type' => 'success',
              'text' => 'Successfully edited journal entry.'
          );
        }

        header('Location: journals.php');
      }
      else {
        //If something doesn't go right, send to home
        header('Location: home.html');
      }

    }
    else if ($_SESSION['logged_in_user']['budgetAccess'] == 'true' && ( isset($_POST['budgetCreate']) || isset($_POST['budgetEdit'])) ){
      //Do queries for budget entry related actions

      //Insert
      if (isset($_POST['budgetCreate']))
      {
        //Since there are two options, determine which needs to be set
        if($_POST['store_name_selector'] === 'other'){
          $store_name = $_POST['new_store_name'];
        }
        else {
          $store_name = $_POST['store_name_selector'];
        }

        $amount = floatval($_POST['amount']);
        $amount_spent = number_format($amount, 2, '.', '');

        $datetime = new DateTime($_POST['date']);
        $datetime->setTime(0, 0, 0);
        $entry_date = $datetime->format('Y-m-d H:i:s');

        $query = 'INSERT INTO budgetLogs (user_id, entry_date, store_name, amount_spent)
                  VALUES (?, ?, ?, ?);';

        $stmt = $link->prepare($query);

        $stmt->bind_param("issd",
                      $_SESSION['logged_in_user']['id'],
                      $entry_date,
                      $store_name,
                      $amount_spent
                    );

        if (!$stmt->execute())
        {
          //If there was an error with executing, display an error
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error adding new budget entry.'
          );
        }
        else {
          //If no errors, display success message
          $_SESSION['message'] = Array('type' => 'success',
              'text' => 'Added new budget entry.'
          );
        }

        header('Location: budgets.php');
      }

      //Update
      else if (isset($_POST['budgetEdit']))
      {
        //Since there are two options, determine which needs to be set
        if($_POST['store_name_selector'] === 'other'){
          $store_name = $_POST['new_store_name'];
        }
        else {
          $store_name = $_POST['store_name_selector'];
        }

        $amount = floatval($_POST['amount']);
        $amount_spent = number_format($amount, 2, '.', '');

        $datetime = new DateTime($_POST['date']);
        $datetime->setTime(0, 0, 0);
        $entry_date = $datetime->format('Y-m-d H:i:s');

        $query = 'UPDATE budgetLogs SET user_id = ? , entry_date = ?, store_name = ?, amount_spent = ? WHERE log_id = ?;';

        $stmt = $link->prepare($query);

        $stmt->bind_param("issdi",
                      $_SESSION['logged_in_user']['id'],
                      $entry_date,
                      $store_name,
                      $amount_spent,
                      $_POST['budget_id_to_edit']
                    );
        if (!$stmt->execute())
        {
          //If there was an error with executing, display an error
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error editing budget entry.'
          );
        }
        else {
          //If no errors, display success message
          $_SESSION['message'] = Array('type' => 'success',
              'text' => 'Successfully edited budget entry.'
          );
        }

        header('Location: budgets.php');
      }
      else {
        //If something doesn't go right, send to home
        header('Location: home.html');
      }

    }
    else if (isset($_POST['accountEdit']) && $_POST['accountEdit'] == 'true'){
      //Editing account information

      //If editing the password, prevents password from being changed if other data is changed
      if (isset($_POST['password']) || isset($_POST['password_confirm']))
      {
        //Check if the password and password confirm match
        if ($_POST['password'] == $_POST['password_confirm'])
        {
          $query = 'UPDATE passwords_for_users SET password = ? WHERE user_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("si",
                        $_POST['password_confirm'],
                        $_SESSION['logged_in_user']['id']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error editing password information.'
            );
            header('Location: account.php');
          }
        }
        else {
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error: Passwords don\'t match.'
          );
          header('Location: account.php');
        }
      }

      //Determine what to enter for access type.
      $student = ($_POST['student_access'] == "studentAccess") ? 'true' : 'false';
      $journal = ($_POST['journal_access'] == "journalAccess") ? 'true' : 'false';
      $budget = ($_POST['budget_access'] == "budgetAccess") ? 'true' : 'false';

      $query = 'UPDATE users SET email = ?, username = ?, studentAccess = ?, journalAccess = ?, budgetAccess = ? WHERE id = ?;';

      $stmt = $link->prepare($query);

      $stmt->bind_param("sssssi",
                    $_POST['email'],
                    $_POST['username'],
                    $student,
                    $journal,
                    $budget,
                    $_SESSION['logged_in_user']['id']
                  );
      if (!$stmt->execute())
      {
        //If there was an error with executing, display an error
        $_SESSION['message'] = Array('type' => 'danger',
            'text' => 'Error editing account information.'
        );
      }
      else {
        //If no errors, display success message
        $_SESSION['message'] = Array('type' => 'success',
            'text' => 'Successfully edited account.'
        );

        //Since everything has been updated, set currently logged in user to the updated information
        $_SESSION['logged_in_user']['email'] = $_POST['email'];
        $_SESSION['logged_in_user']['usernmame'] = $_POST['username'];
        $_SESSION['logged_in_user']['studentAccess'] = $student;
        $_SESSION['logged_in_user']['journalAccess'] = $journal;
        $_SESSION['logged_in_user']['budgetAccess'] = $budget;

      }
      header('Location: account.php');
    }
    else{
      //Is logged in but something is wrong with accessing any of the access types
      $_SESSION['message'] = Array('type' => 'danger',
          'text' => 'Error with access. Please try again.'
      );
      header('Location: home.html');
    }
}
//Actions for not logged in users (those creating an account)
else if (isset($_POST['accountCreate']))
{
  require_once('db.php');

  //Check if the passwords entered actually match one another.
  if ($_POST['password'] != $_POST['password_confirm'])
  {
    $_SESSION['message'] = Array('type' => 'danger',
        'text' => 'Error: Passwords entered do not match.'
    );
    header('Location: create_account.php');
  }
  else {
    //If passwords match, proceed to add new user
    $student = ($_POST['student_access'] == "studentAccess") ? 'true' : 'false';
    $journal = ($_POST['journal_access'] == "journalAccess") ? 'true' : 'false';
    $budget = ($_POST['budget_access'] == "budgetAccess") ? 'true' : 'false';

    $query = 'INSERT INTO users (id, email, username, studentAccess, journalAccess, budgetAccess)
              VALUES (NULL, ?, ?, ?, ?, ?);';

    $stmt = $link->prepare($query);

    $stmt->bind_param("sssss",
                  $_POST['email'],
                  $_POST['username'],
                  $student,
                  $journal,
                  $budget,
                );
    if (!$stmt->execute())
    {
      //If there was an error with executing, display an error
      $_SESSION['message'] = Array('type' => 'danger',
          'text' => 'Error adding new user\'s information, please re-enter the information.'
      );
      header('Location: create_account.php');
    }
    else {
      //If no errors, query to get the new user id that was just created
      $query = 'SELECT id FROM users WHERE email = ? AND username = ? LIMIT 1;';

      $stmt = $link->prepare($query);

      $stmt->bind_param("ss",
                    $_POST['email'],
                    $_POST['username']
                  );
      if (!$stmt->execute())
      {
        //If there was an error with executing, display an error
        $_SESSION['message'] = Array('type' => 'danger',
            'text' => 'Error: unable to find the new user.'
        );
        header('Location: create_account.php');
      }
      else {
        //Get the user ID
        $stmt->bind_result($user_id);
        $stmt->fetch();
        $stmt->close();

        //Insert the password into the database with the appropriate user id
        $query = 'INSERT INTO passwords_for_users (id, user_id, password)
                  VALUES (NULL, ?, ?);';

        $stmt = $link->prepare($query);

        $stmt->bind_param("is",
                      $user_id,
                      $_POST['password_confirm']
                    );
        if (!$stmt->execute())
        {
          //If there was an error with executing, display an error
          $_SESSION['message'] = Array('type' => 'danger',
              'text' => 'Error: adding new user\'s password, please re-enter the information.'
          );
          header('Location: create_account.php');
        }
        else {
          //Upon successfully added, log in the new user with the Information
          $query = 'SELECT * FROM users WHERE id = ? AND username = ? LIMIT 1;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("is",
                        $user_id,
                        $_POST['username']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, display an error
            $_SESSION['message'] = Array('type' => 'danger',
                'text' => 'Error: logging new user in. Please try again.'
            );
            header('Location: login.html');
          }
          else {
            $stmt->bind_result($user_id, $email, $username, $student, $journal, $budget);
            $stmt->fetch();
            $stmt->close();
            $_SESSION['logged_in_user']['id'] = $user_id;
            $_SESSION['logged_in_user']['email'] = $_POST['email'];
            $_SESSION['logged_in_user']['usernmame'] = $_POST['username'];
            $_SESSION['logged_in_user']['studentAccess'] = $student;
            $_SESSION['logged_in_user']['journalAccess'] = $journal;
            $_SESSION['logged_in_user']['budgetAccess'] = $budget;

            header('Location: home.html');
          }
        }
      }
    }
  }
}
else{
  //If not properly logged in, send to login page
  header('Location: login.html');
}

?>
