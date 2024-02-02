<?php
		session_start();
		require_once('db.php');

		if (isset($_SESSION['message'])) {
			$message = $_SESSION['message'];
			unset($_SESSION['message']);
		}
		error_log(var_export($_SESSION, true));

		if (isset($_SESSION['logged_in_user']['id']) && isset($_SESSION['logged_in_user']['email'])
				&& isset($_SESSION['logged_in_user']['username']) && isset($_SESSION['logged_in_user']['studentAccess'])
				&& isset($_SESSION['logged_in_user']['journalAccess']) && isset($_SESSION['logged_in_user']['budgetAccess']))
		{
			//Before any information gets displayed, make sure that all of the information is properly set.
			$user_id = $_SESSION['logged_in_user']['id'];
			$email = $_SESSION['logged_in_user']['email'];
			$username = $_SESSION['logged_in_user']['username'];
			$student_access = $_SESSION['logged_in_user']['studentAccess'];
			$journal_access = $_SESSION['logged_in_user']['journalAccess'];
			$budget_access = $_SESSION['logged_in_user']['budgetAccess'];

			$account_update = '<input type="hidden" value="true" name="account" />';
			$account_update .= '<input type="hidden" value="true" name="accountEdit" />';
		}
		else {
			//If there is nothng set, assume bad access and go to login
			header('Location: login.html');
		}
?>

<?php
include('header.html');
?>


<main role="main" class="container">

	<div class="container text-center">
		<h1>My Account</h1>
		<br><br>
		<h2>User Information</h2>
		<?php
		if (isset($message)) {
				echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
		}
		?>
		<hr>
		<form action="crud.php" method="post" />

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control" value="<?=$username?>"></input>
                </div>
            </div>
        </div>

				<div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" class="form-control" value="<?=$email?>"></input>
                </div>
            </div>
        </div>

				<div class="row">
	          <div class="col-12">
	              <div class="form-group">
	                <label for="account_access">Log Type Access:</label>
									<br>
	                <div class="checkbox-containeer form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="student_access" id="student_access" value="studentAccess" <?=( ($student_access == "true") ? 'checked' : '')?> >
	                    <label class="form-check-label" for="student_access"> Education and Student Logs</label>
	                </div>
	                <div class="checkbox-container form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="journal_access" id="journal_access" value="journalAccess" <?=( ($journal_access == "true") ? 'checked' : '')?> >
	                    <label class="form-check-label" for="journal_access"> Journal Entries </label>
	                </div>
									<div class="checkbox-container form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="budget_access" id="budget_access" value="budgetAccess" <?=( ($budget_access == "true" ) ? 'checked' : '')?> >
	                    <label class="form-check-label" for="budget_access"> Budget Entries</label> <br><br>
	                </div>
	              </div>
	          </div>
	      </div>

				<hr>
				<div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="password">New Password:</label>
                    <input type="password" name="password" id="password" class="form-control"></input>
                </div>
            </div>
        </div>

				<div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="password_confirm">Password Confirm:</label>
                    <input type="password" name="password_confirm" id="password_confirm" class="form-control"></input>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <div class="form-actions">
										<?=$account_update?>
                    <button type="submit" class="btn btn-primary btn-lg" id="save_button">Save Changes</button>
                </div>
            </div>
        </div>

    </form>
		<hr>
		<form class="form my-2 my-lg-0">
				<a href="index.php?logout=yes" class="btn btn-danger btn-lg" type="submit">Log Out</a>
		</form>
	</div>


</main>



<?php
    //include('footer.html');
?>

<link rel="stylesheet" href="includes/bootstrap-select/css/bootstrap-select.min.css">
<script src="includes/jquery-3.3.1.min.js"></script>
<script src="includes/bootstrap.js" ></script>
<script src="includes/bootstrap-select/js/bootstrap-select.min.js"></script>

<script>
	const checkboxes = document.querySelectorAll('.form-check-checkbox');
    checkboxes.forEach((checkbox) => {
        checkbox.addEventListener('change', (event) => {
            if (event.target.checked) {
                checkboxes.forEach((cb) => {
                    if (cb !== event.target) {
                        cb.checked = false;
                    }
                });
            }
        });
    });

		document.getElementById('password_confirm').addEventListener('input', function() {
		  var password = document.getElementById('password').value;
		  var passwordConfirm = this.value;

		  if (password === passwordConfirm) {
		    // Passwords match
		    this.style.borderColor = 'green';
		  } else {
		    // Passwords do not match
		    this.style.borderColor = 'red';
		  }
		});



</script>

</body></html>
