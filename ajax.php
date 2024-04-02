<?php
// Filename: ajax.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Delete information from the database in an async call
session_start();
require_once('db.php');


if ($_SESSION['logged_in_user']['id'] > 0 && isset($_POST['action']) )  {

    switch ($_POST['action']) {
        case 'delete_log':
          $query = 'DELETE FROM educationLogs WHERE log_id = ? AND user_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("ii",
                        $_POST['log_id_to_delete'],
                        $_SESSION['logged_in_user']['id']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing
            echo 'nay'; die();
          }
          else {
            //If no errors
            echo 'yay'; die();
          }
          break;

        case 'delete_student':
          //Go through the list and delete all student logs where the student was so that there is no missing information
          //being displayed to the user
          $query = 'DELETE FROM educationLogs WHERE student_id = ? AND user_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("ii",
                        $_POST['student_id_to_delete'],
                        $_SESSION['logged_in_user']['id']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing, kill the process
            echo 'nay'; die();
          }
          else {
            //If no errors, proceed deleting student
            $query = 'DELETE FROM students WHERE student_id = ? AND user_id = ?;';

            $stmt = $link->prepare($query);

            $stmt->bind_param("ii",
                          $_POST['student_id_to_delete'],
                          $_SESSION['logged_in_user']['id']
                        );
            if (!$stmt->execute())
            {
              //If there was an error with executing
              echo 'nay'; die();
            }
            else {
              //If no errors
              echo 'yay'; die();
            }
          }
          break;

        case 'delete_journal':
          $query = 'DELETE FROM journalLogs WHERE log_id = ? AND user_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("ii",
                        $_POST['journal_id_to_delete'],
                        $_SESSION['logged_in_user']['id']
                      );
          if (!$stmt->execute())
          {
            //If there was an error with executing
            echo 'nay'; die();
          }
          else {
            //If no errors
            echo 'yay'; die();
          }
          break;

        case 'delete_budget':
          $query = 'DELETE FROM budgetLogs WHERE log_id = ? AND user_id = ?;';

          $stmt = $link->prepare($query);

          $stmt->bind_param("ii",
                      $_POST['budget_id_to_delete'],
                      $_SESSION['logged_in_user']['id']
                    );
          if (!$stmt->execute())
          {
            //If there was an error with executing
            echo 'nay'; die();
          }
          else {
            //If no errors
            echo 'yay'; die();
          }
          break;

        default:
            die("nope!");
            break;
    }
}
else {
    die("nope!");
}
