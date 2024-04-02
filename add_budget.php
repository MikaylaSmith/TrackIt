<?php
// Filename: add_budget.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Create or edit a budget log then write to the database

session_start();


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}


if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['budgetAccess'] == 'true') {
    require_once('db.php');
    $editing = false;

    //Check if viewing/editing is set
    if (isset($_GET['edit_budget']) && $_GET['edit_budget'] > 0){
      $editing = true;
      //Get the information for the student to be edited
      $budget_query = "SELECT * FROM budgetLogs
                        WHERE log_id = '".mysqli_real_escape_string($link, $_GET['edit_budget'])."'
                        AND user_id = '".$_SESSION['logged_in_user']['id']."';";
      $budget_result = mysqli_query($link, $budget_query);

      $budget_to_edit = mysqli_fetch_assoc($budget_result);

      //Populate variables that will be displayed
      $datetime = new DateTime($budget_to_edit['entry_date']);
      $date_to_edit = $datetime->format('Y-m-d');

      $store_name_to_edit = $budget_to_edit['store_name'];
      $amount_to_edit = $budget_to_edit['amount_spent'];
    }

    //When sent to do queries with the data, set what action it will be
    if ($editing)
    {
      $budget_action_type = '<input type="hidden" value="true" name="budgetEdit" />';
      $budget_action_type .= '<input type="hidden" value="'.$_GET['edit_budget'].'" name="budget_id_to_edit" /> ';
    }
    else{
      $budget_action_type = '<input type="hidden" value="true" name="budgetCreate" />';
    }

    $store_list_query = "SELECT store_name
                          FROM budgetLogs
                          WHERE user_id = '".$_SESSION['logged_in_user']['id']."' GROUP BY store_name ORDER BY store_name ASC;";
    $store_list_result = mysqli_query($link, $store_list_query);

    while($store = mysqli_fetch_assoc($store_list_result)){
      $store_name = $store['store_name'];
      $selected = '';
      if($editing && ($store_name == $store_name_to_edit)){
          $selected = ' selected=selected ';
      }
      $store_name_options .= '<option value="'.$store_name.'"'.$selected.'>'.$store_name.'</option>';
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
        <h1><?=($editing ? 'Update a' : 'Add a New')?> Budget Entry</h1>
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
                    <label for="store_name_selector">Store Name:</label>
                    <select class="browser-default custom-select mb-2 form-control selectpicker" data-live-search="true"
                            name="store_name_selector" id="store_name_selector" title="Select Store Name"
                            data-selected-text-format="count > 4" >
                          <option value="">Select an existing store</option>
                          <?=$store_name_options?>
                          <option value="other">Other (please specify)</option>
                    </select>
                    <input type="text" name="new_store_name" id="new_store_name" class="form-control" placeholder="Enter new store name"
                          style="display: none;" />
                    <div class="invalid-feedback">
                        Please choose a store name or enter a new one.
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label for="date">Date of Transaction:</label>
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
                    <label for="amount">Amount Spent:</label>
                    <div class="input-group">
                        <!-- <div class="input-group-prepend">
                            <span class="input-group-text">$</span>
                        </div> -->
                        <input type="text" name="amount" id="amount" class="form-control" placeholder="Amount Spent" value="<?=$amount_to_edit?>"/>
                        <div class="invalid-feedback">
                            Please enter an amount spent at the store.
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col text-center">
                <div class="form-actions">
                    <?=$budget_action_type?>
                    <button type="submit" class="btn btn-primary btn-lg" id="submit_button"><?=($editing ? 'Update' : 'Add')?> Budget Entry</button>
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
  // document.getElementById('amount').addEventListener('input', function(event) {
  //   let amount = parseFloat(this.value);
  //   if (!isNaN(amount)) {
  //       let formattedAmount = new Intl.NumberFormat('en-US', { style: 'currency', currency: 'USD' }).format(amount);
  //       this.value = formattedAmount;
  //   }
  // });

  document.getElementById('amount').addEventListener('input', function(event) {
    // Remove any non-numeric characters and leading zeros
    this.value = this.value.replace(/[^0-9.]/g, '');
  });

  document.getElementById('store_name_selector').addEventListener('change', function() {
    if (this.value === 'other') {
        document.getElementById('new_store_name').style.display = 'block';
    } else {
        document.getElementById('new_store_name').style.display = 'none';
    }
  });

</script>
</body></html>
