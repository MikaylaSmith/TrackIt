<?php
// Filename: students.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Query the database and list all entered student information
session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['studentAccess'] == 'true') {
    require_once('db.php');

    $user_id = $_SESSION['logged_in_user']['id'];

    $student_array = array();

    $query = "SELECT * FROM students WHERE user_id = '".$user_id."';";

    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) >= 1) {
        while ($student = mysqli_fetch_assoc($result)) {
            $students_table .= '<tr id="student_row_'.$student['student_id'].'">
                <td>'.$student['student_name'].'</td>
                <td>'.$student['school'].'</td>
                <td>'.$student['grade'].'</td>
                <td><a class="btn btn-sm btn-info" href="add_student.php?edit_student='.$student['student_id'].'">View</a></td>
                <td><button class="btn btn-sm btn-danger" onclick=delete_student(\''.$student['student_id'].'\')>Delete</button></td>
            </tr>';
        }
    }
}
else {
    header('Location: login.html');
}

?>
<?php
include('header.html');
?>
<main role="main" class="container">
    <div class="container text-center">
        <h1>Students</h1>
        <a class="btn btn-primary" href="add_student.php" role="button">Add New Student</a>
    </div>
    <?php
    if (isset($message)) {
        //error_log("login sees message as: $message");
        echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
    }
    ?>
    <div class="row">
        <div class="col-12">
            <table class="table table-striped table-hover" id="students_table">
                <thead>
                <tr>
                    <th scope="col">Student Name</th>
                    <th scope="col">Current School</th>
                    <th scope="col">Current Grade</th>
                    <th scope="col">View</th>
                    <th scope="col">Delete</th>
                </tr>
                </thead>
                <tbody>
                    <?=$students_table?>
                </tbody>
            </table>
        </div>
    </div>
    <br/><br/>
</main><!-- /.container -->
    <?php
    include('footer.html');
    ?>
<script src="includes/jquery-3.3.1.min.js"></script>
<script src="includes/bootstrap.js" ></script>
<link rel="stylesheet" type="text/css" href="includes/DataTables/datatables.min.css"/>
<script type="text/javascript" src="includes/DataTables/datatables.min.js"></script>
<script>
$(function() {
    var table = $('#students_table').DataTable({
    });
    table.buttons().container().appendTo('#students_table_wrapper .col-md-6:eq(0)');
});


function delete_student(id) {
    let confirmation = confirm('Are you sure you want to delete this student?');

    if (confirmation) {
        $.ajax({
            method: "POST",
            url: "ajax.php",
            data: { action: "delete_student", student_id_to_delete: id }
        })
            .done(function( msg ) {
                if (msg == 'yay') {
                    let table = $('#students_table').DataTable();
                    let selector = 'student_row_'+id;
                    let row_to_remove = $('#'+selector);
                    table.row(row_to_remove).remove().draw();
                    alert("Student Deleted");
                }
                else if (msg == 'nay') {
                    alert("Error: Unable to Delete Student");
                }
            });
    }

}
</script>
</body></html>
