<?php
// Filename: create_account.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Create brand new account without having access to the rest of the site.

	include('header.html');
?>


<main role="main" class="container">

	<div class="container text-center">

		<h1>Create a New Account</h1>
		<hr>
		<?php
		if (isset($message)) {
				echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
		}
		?>
		<a href="login.html">Go Back</a>
		<form action="crud.php" method="post" />

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="username">Username:</label>
                    <input type="text" name="username" id="username" class="form-control"></input>
                </div>
            </div>
        </div>

				<div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="text" name="email" id="email" class="form-control"></input>
                </div>
            </div>
        </div>

				<div class="row">
	          <div class="col-12">
	              <div class="form-group">
	                <label for="account_access">Access Type:</label>
									<br>
	                <div class="checkbox-containeer form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="student_access" id="student_access" value="studentAccess">
	                    <label class="form-check-label" for="student_access"> Education and Student Logs</label>
	                </div>
	                <div class="checkbox-container form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="journal_access" id="journal_access" value="journalAccess">
	                    <label class="form-check-label" for="journal_access"> Journal Entries </label>
	                </div>
									<div class="checkbox-container form-check-inline">
	                    <input class="form-check-checkbox" type="checkbox" name="budget_access" id="budget_access" value="budgetAccess">
	                    <label class="form-check-label" for="budget_access"> Budget Entries</label> <br><br>
	                </div>
	              </div>
	          </div>
	      </div>

				<div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="password">Password:</label>
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

				<br>
				<hr>

        <div class="row">
            <div class="col text-center">
                <div class="form-actions">
										<input type="hidden" value="true" name="accountCreate" />
                    <button type="submit" class="btn btn-primary btn-lg" id="submit_button">Create Account</button>
                </div>
            </div>
        </div>
    </form>
	</div>


</main>

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
