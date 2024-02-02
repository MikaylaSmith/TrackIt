<?php

session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}


if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['studentAccess'] == 'true') {
    require_once('db.php');
    $editing = false;

    $user_id = $_SESSION['logged_in_user']['id'];

    //Check if viewing/editing is set
    if (isset($_GET['edit_log']) && $_GET['edit_log'] > 0){
      $editing = true;

      $edit_log_id = mysqli_real_escape_string($link, $_GET['edit_log']);

      //Get the information for the log to be edited, and ensure that the logged in user is the only one able to access it
      $log_query = "SELECT * FROM educationLogs WHERE log_id = '".$edit_log_id."' AND user_id = '".$user_id."';";
      $log_result = mysqli_query($link, $log_query);
      if (mysqli_num_rows($log_result) == 0)
      {
        $_SESSION['message'] = Array('type' => 'danger',
            'text' => 'You do not have access to that log.'
        );
        header('Location: logs.php');
      }
      else {
        $log_to_edit = mysqli_fetch_assoc($log_result);
      }

      //Populate variables that will be displayed
      $student_to_edit = $log_to_edit['student_id'];
      $school_to_edit = $log_to_edit['school'];
      $grade_to_edit = $log_to_edit['grade'];
      $minutes_to_edit = $log_to_edit['session_time'];
      $notes_to_edit = $log_to_edit['notes'];

      //Format the date away from the time
      $datetime = new DateTime($log_to_edit['log_date']);
      $date_to_edit = $datetime->format('Y-m-d');
    }

    //Get a list of all of the students that are assigned to the signed in user
    $query = "SELECT student_id, student_name FROM students WHERE user_id = '".$user_id."' ORDER BY student_name ASC;";
    $students_result = mysqli_query($link, $query);

    if (mysqli_num_rows($students_result) > 0){
      while($student = mysqli_fetch_assoc($students_result)){
        $id = $student['student_id'];
        $student_name = $student['student_name'];

        $selected = '';
        if($editing && ($id == $student_to_edit)){
            $selected = ' selected=selected ';
        }
        $student_name_options .= '<option value="'.$id.'"'.$selected.'>'.$student_name.'</option>';
      }
    }

    //Session times, with easy to read for larger than 60 minutes
	  for ($i = 1; $i <= 16; $i++) {
		    $minutes = $i * 15; //15 minute increments
		    if ($i > 4) {
			       $hours_display = $minutes / 60;
			       $minutes_display = $minutes." minutes ($hours_display hours)";
		    }
		    else {
			        $minutes_display = $minutes." minutes";
		    }
		    $minute_options .= '<option value='.$minutes.' '.($minutes_to_edit == $minutes ? 'selected=selected':'').'>'.$minutes_display.'</option>';
	  }

    //When sent to do queries with the data, set what action it will be
    if ($editing)
    {
      $log_action_type = '<input type="hidden" value="true" name="educationEdit" />';
      $log_action_type .= '<input type="hidden" value="'.$edit_log_id.'" name="log_id_to_edit" /> ';
    }
    else{
      $log_action_type = '<input type="hidden" value="true" name="educationCreate" />';
    }

}
else {
    header('Location: login.html');
}

?>
<?php
include('header.html');
?>

<head>
  <style>
    .grid-container{
      display: grid;
      grid-template-columns: 1fr 1fr 1fr 1fr;
      row-gap: 50px;
    }
  </style>
</head>

<main role="main" class="container">

    <div class="container text-center">
        <h1><?=($editing ? 'Update A' : 'Add New')?> Log</h1>
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
                    <label for="students">Student Name</label>
                    <select class="browser-default custom-select mb-2 form-control selectpicker" data-live-search="true"
                            id="select" name="student" id="student" title="Select Student"
                            data-selected-text-format="count > 4" >
                        	<?=$student_name_options?>
                    </select>
                    <div class="invalid-feedback">
                        Please choose a student.
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" name="date" id="date" style="max-width:170px" max="<?=(date('Y-m-d'))?>" class="form-control" value="<?=$date_to_edit?>"/>
                    <div class="invalid-feedback">
                        Please choose a date.
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
              <div class="col-12">
                  <div class="form-group">
                      <label for="school">School:</label>
                      <input type="text" name="school" id="school" class="form-control" value="<?=$school_to_edit?>"/>
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
                      <input type="number" name="grade" id="grade" class="form-control" min="1" value="<?=$grade_to_edit?>"/>
                      <div class="invalid-feedback">
                          Please enter a grade.
                      </div>
                  </div>
              </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group mb-0">
                    <label for="minutes">Length of Session (minutes):</label>
                    <select class="browser-default custom-select mb-2 form-control" id="select" name="minutes" id="minutes">
                        <option value="" disabled="" selected="">For how long?</option>
                        <?=$minute_options?>
                    </select>
                    <div class="invalid-feedback">
                        Please choose the length of the session.
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4" id="note_row">
            <div class="col-12">
                <div class="form-group">
                    <label for="note">Notes:</label>
                    <textarea name="note" id="note" class="form-control" rows="5"><?=$notes_to_edit?></textarea>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <div class="form-actions">
                  <?=$log_action_type?>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit_button"><?=($editing ? 'Update' : 'Add')?> Log</button>
                </div>
            </div>
        </div>

    </form>

</main><!-- /.container -->
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
