<?php
// Filename: add_journal.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Create or edit a journal entry then write to the database
session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}


if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['journalAccess'] == 'true') {
    require_once('db.php');
    $editing = false;

    //Check if viewing/editing is set
    if (isset($_GET['edit_journal']) && $_GET['edit_journal'] > 0){
      $editing = true;

      $edit_journal_id = mysqli_real_escape_string($link, $_GET['edit_journal']);
      //Get the information for the journal to be edited
      $journal_query = "SELECT * FROM journalLogs WHERE log_id = '".$edit_journal_id."';";
      $journal_result = mysqli_query($link, $journal_query);

      $journal_to_edit = mysqli_fetch_assoc($journal_result);

      //Populate variables that will be displayed
      $title_to_edit = $journal_to_edit['title'];
      $notes_to_edit = $journal_to_edit['notes'];
    }

    //When sent to do queries with the data, set what action it will be
    if ($editing)
    {
      $journal_action_type = '<input type="hidden" value="true" name="journalEdit" />';
      $journal_action_type .= '<input type="hidden" value="'.$edit_journal_id.'" name="journal_id_to_edit" /> ';
    }
    else{
      $journal_action_type = '<input type="hidden" value="true" name="journalCreate" />';
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
        <h1><?=($editing ? 'Update A' : 'Add New')?> Journal Entry</h1>
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
                      <label for="title">Title:</label>
                      <input type="text" name="title" id="title" class="form-control" value="<?=$title_to_edit?>"/>
                      <div class="invalid-feedback">
                          Please enter a title.
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
                    <?=$journal_action_type?>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit_button"><?=($editing ? 'Update' : 'Add')?> Journal Entry</button>
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

</script>
</body></html>
