<?php
// Filename: add_student.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Create or edit a student's information then write to the database
session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}


if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['studentAccess'] == 'true') {
    require_once('db.php');
    $editing = false;

    //Check if viewing/editing is set
    if (isset($_GET['edit_student']) && $_GET['edit_student'] > 0){
      $editing = true;
      //Get the information for the student to be edited
      $student_query = "SELECT * FROM students
                        WHERE student_id = '".mysqli_real_escape_string($link, $_GET['edit_student'])."'
                        AND user_id = '".$_SESSION['logged_in_user']['id']."';";
      $student_result = mysqli_query($link, $student_query);

      $student_to_edit = mysqli_fetch_assoc($student_result);

      //Populate variables that will be displayed
      $student_name_to_edit = $student_to_edit['student_name'];
      $school_to_edit = $student_to_edit['school'];
      $grade_to_edit = $student_to_edit['grade'];
    }

    //When sent to do queries with the data, set what action it will be
    if ($editing)
    {
      $student_action_type = '<input type="hidden" value="true" name="studentEdit" />';
      $student_action_type .= '<input type="hidden" value="'.$_GET['edit_student'].'" name="student_id_to_edit" /> ';
    }
    else{
      $student_action_type = '<input type="hidden" value="true" name="studentCreate" />';
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
        <h1><?=($editing ? 'Update a' : 'Add a New')?> Student</h1>
    </div>
    <?php
    if (isset($message)) {
        echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
    }
    ?>
    <form action="crud.php" method="post" />

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="name">Name:</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Student Name" value="<?=$student_name_to_edit?>">
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                  <label for="school">School:</label>
                    <input type="text" name="school" id="school" class="form-control" placeholder="School Name" value="<?=$school_to_edit?>"/>
                    <div class="invalid-feedback">
                      Please enter a school.
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                  <label for="grade">Grade:</label>
                    <input type="number" name="grade" id="grade" class="form-control" placeholder="Grade Level" min="1" pattern="[0-9]" inputmode="numeric" value="<?=$grade_to_edit?>"/>
                      <div class="invalid-feedback">
                        Please enter a grade.
                      </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <div class="form-actions">
                    <?=$student_action_type?>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit_button"><?=($editing ? 'Update' : 'Add')?> Student</button>
                </div>
            </div>
        </div>
    </form>

</main>
<link rel="stylesheet" href="includes/bootstrap-select/css/bootstrap-select.min.css">
<script src="includes/jquery-3.3.1.min.js"></script>
<script src="includes/bootstrap.js" ></script>
<script src="includes/bootstrap-select/js/bootstrap-select.min.js"></script>
<script>
  document.getElementById('grade').addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
  });
</script>
</body></html>
