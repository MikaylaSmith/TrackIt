<?php
// Filename: budget_totals.php
// Author: Mikayla Smith
// Date: 04/02/2024
// Purpose:
// Query the database and calculate the total amount spent per store
// based on what has been entered

session_start();
require_once('db.php');


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['budgetAccess'] == 'true') {
    require_once('db.php');

    $user_id = $_SESSION['logged_in_user']['id'];

    $query = "SELECT log_id, store_name, SUM(amount_spent) AS total_amount FROM budgetLogs WHERE user_id = '".$user_id."' GROUP BY store_name ORDER BY store_name ASC;";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) >= 1) {
      $budget_headers = '<tr>
                        <th scope="col">Store Name </th>
                        <th scope="col">Total Amount Spent ($)</th>
                      </tr>';


      while ($entry = mysqli_fetch_assoc($result)) {

        //Populate the table with the proper data
        $budget_table .= '<tr id="log_row_'.$entry['log_id'].'">
                            <td>'.$entry['store_name'].'</td>
                            <td>'.$entry['total_amount'].'</td>
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
         <h1>Totals By Store</h1>
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
         <table class="table table-striped table-hover" id="budgets_totals_table">
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
        var table = $('#budgets_totals_table').DataTable({
        });

        table.buttons().container().appendTo('#budgets_totals_table_wrapper .col-md-6:eq(0)');
    });

</script>

</body></html>
