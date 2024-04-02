<?php
// Filename: budgets.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Query the databse and list out each individual entry that has been made
session_start();
require_once('db.php');


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['budgetAccess'] == 'true') {
    require_once('db.php');

    $user_id = $_SESSION['logged_in_user']['id'];

    $query = "SELECT * FROM budgetLogs WHERE user_id = '".$user_id."' ORDER BY entry_date DESC, store_name ASC;";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) >= 1) {
      $budget_headers = '<tr>
                        <th scope="col">Date</th>
                        <th scope="col">Store Name </th>
                        <th scope="col">Amount Spent ($)</th>
                        <th scope="col">View</th>
                        <th scope="col">Delete</th>
                      </tr>';


      while ($entry = mysqli_fetch_assoc($result)) {

        //Separate the time from the date
        $datetime = new DateTime($entry['entry_date']);
        $entry_date = $datetime->format('Y-m-d');

        //Populate the table with the proper data
        $budget_table .= '<tr id="log_row_'.$entry['log_id'].'">
                            <td>'.$entry_date.'</td>
                            <td>'.$entry['store_name'].'</td>
                            <td>'.$entry['amount_spent'].'</td>
                            <td><a class="btn btn-sm btn-info" href="add_budget.php?edit_budget='.$entry['log_id'].'">View</a></td>
                            <td><a class="btn btn-sm btn-danger" href="#" onClick="delete_budget(\''.$entry['log_id'].'\')">Delete</a></td>
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
         <h1>Budget Entries</h1>
         <a class="btn btn-primary" href="add_budget.php" role="button">Add New Entry</a>
     </div>

     <?php
     if (isset($message)) {
         echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
     }
     ?>

     <?php
     if (isset($budget_headers, $budget_table)):
    ?>
    <div class="row">
      <div class="col-12">
         <table class="table table-striped table-hover" id="budgets_table">
             <thead>
                 <?=$budget_headers?>
             </thead>
             <tbody>
             <?=$budget_table?>
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
        var table = $('#budgets_table').DataTable({
        });

        table.buttons().container().appendTo('#budgets_table_wrapper .col-md-6:eq(0)');
    });


    function delete_budget(id) {
        let confirmation = confirm('Are you sure you want to delete this entry?');

        if (confirmation) {
            $.ajax({
                method: "POST",
                url: "ajax.php",
                data: { action: "delete_budget", budget_id_to_delete: id }
            })
                .done(function( msg ) {
                    if (msg == 'yay') {
                        let table = $('#budgets_table').DataTable();
                        let selector = 'log_row_'+id;
                        let row_to_remove = $('#'+selector);
                        table.row(row_to_remove).remove().draw();
                        alert("Entry Deleted");
                    }
                    else if (msg == 'nay') {
                        alert("Error: Unable to Delete Entry");
                    }
                });
        }

    }


</script>

</body></html>
