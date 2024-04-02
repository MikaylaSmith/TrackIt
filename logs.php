<?php
// Filename: logs.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Query the databse and list all of the entered education logs
session_start();
require_once('db.php');


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['studentAccess'] == 'true') {
    require_once('db.php');

    $user_id = $_SESSION['logged_in_user']['id'];

    //Get a list of students from the database who are assigned to the logged in user
    $student_list_array = array();

    $student_list_query = "SELECT * FROM students WHERE user_id = '".$user_id."';";
    $student_list_result = mysqli_query($link, $student_list_query);
    while($student = mysqli_fetch_assoc($student_list_result)){
      $id = $student['student_id'];
      $student_list_array[$id] = $student;
    }

    $query = "SELECT * FROM educationLogs WHERE user_id = '".$user_id."' ORDER BY log_date DESC;";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) >= 1) {
      $log_headers = '<tr>
                        <th scope="col">Date</th>
                        <th scope="col">Student Name </th>
                        <th scope="col">School</th>
                        <th scope="col">Grade Level</th>
                        <th scope="col">Session Minutes</th>
                        <th scope="col">View</th>
                        <th scope="col">Delete</th>
                      </tr>';


      while ($log = mysqli_fetch_assoc($result)) {
        $id = $log['student_id'];
        $student_info = $student_list_array[$id];

        //Separate the time from the date
        $datetime = new DateTime($log['log_date']);
        $log_date = $datetime->format('Y-m-d');

        //Populate the table with the proper data
        $log_table .= '<tr id="log_row_'.$log['log_id'].'">
                            <td>'.$log_date.'</td>
                            <td>'.$student_info['student_name'].'</td>
                            <td>'.$log['school'].'</td>
                            <td>'.$log['grade'].'</td>
                            <td>'.$log['session_time'].'</td>
                            <td><a class="btn btn-sm btn-info" href="add_log.php?edit_log='.$log['log_id'].'">View</a></td>
                            <td><button class="btn btn-sm btn-danger" onclick=delete_log(\''.$log['log_id'].'\')>Delete</button></td>
                        </tr>';

      }
    }
  }

 ?>

 <?php

 include('header.html');

 ?>

 <main role="main" class="container">

     <div class="container text-center">
         <h1>Education Logs</h1>
         <a class="btn btn-primary" href="add_log.php" role="button">Add New Log</a>
     </div>

     <?php
     if (isset($message)) {
         echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
     }
     ?>

     <?php
     if (isset($log_headers, $log_table)):
    ?>
    <div class="row">
      <div class="col-12">
         <table class="table table-striped table-hover" id="log_table">
             <thead>
                 <?=$log_headers?>
             </thead>
             <tbody>
             <?=$log_table?>
             </tbody>
         </table>
      </div>
    </div>

    <div class="row">
      <div class="col-12">
          <div id="buttons_go_here"></div>
      </div>
    </div>
    <?php
    endif;
    ?>

</main>
<?php
include('footer.html');
?>
<script src="includes/jquery-3.3.1.min.js"></script>
<script src="includes/bootstrap.js" ></script>
<link rel="stylesheet" type="text/css" href="includes/DataTables/datatables.min.css"/>
<script type="text/javascript" src="includes/DataTables/datatables.min.js"></script>
<script>


    $(function() {
        var table = $('#log_table').DataTable({
        });
        table.buttons().container().appendTo('#log_table_wrapper .col-md-6:eq(0)');
    });


    function delete_log(id) {
        let confirmation = confirm('Are you sure you want to delete this log?');

        if (confirmation) {
            $.ajax({
                method: "POST",
                url: "ajax.php",
                data: { action: "delete_log", log_id_to_delete: id }
            })
                .done(function( msg ) {
                    if (msg == 'yay') {
                        let table = $('#log_table').DataTable();
                        let selector = 'log_row_'+id;
                        let row_to_remove = $('#'+selector);
                        table.row(row_to_remove).remove().draw();
                        alert("Log Deleted");
                    }
                    else if (msg == 'nay') {
                        alert("Error: Unable to Delete Log");
                    }
                });
        }

    }


</script>

</body></html>
