<?php

session_start();
require_once('db.php');


if (isset($_SESSION['message'])) {
    $message = $_SESSION['message'];
    unset($_SESSION['message']);
}

if ($_SESSION['logged_in_user']['id'] > 0 && $_SESSION['logged_in_user']['journalAccess'] == 'true') {
    require_once('db.php');

    $user_id = $_SESSION['logged_in_user']['id'];

    $query = "SELECT * FROM journalLogs WHERE user_id = '".$user_id."' ORDER BY title ASC;";
    $result = mysqli_query($link, $query);
    if (mysqli_num_rows($result) >= 1) {
      $journal_headers = '<tr>
                        <th scope="col">Title</th>
                        <th scope="col">Notes</th>
                        <th scope="col">View</th>
                        <th scope="col">Delete</th>
                      </tr>';


      while ($journal = mysqli_fetch_assoc($result)) {
        $notes = $journal['notes'];
        $words = str_word_count($notes, 1, '1234567890');

        $dots_string = '';
        if (count($words) > 5){
          $dots_string = "...";
        }

        $entry_notes_preview = implode(' ', array_slice($words, 0 , 5));
        $entry_notes_preview .= $dots_string;

        //Populate the table with the proper data
        $journal_table .= '<tr id="journal_row_'.$journal['log_id'].'">
                            <td>'.$journal['title'].'</td>
                            <td>'.nl2br($entry_notes_preview).'</td>
                            <td><a class="btn btn-sm btn-info" href="add_journal.php?edit_journal='.$journal['log_id'].'">View</a></td>
                            <td><a class="btn btn-sm btn-danger" href="#" onClick="delete_journal(\''.$journal['log_id'].'\')">Delete</a></td>
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
         <h1>Journal Entries</h1>
         <a class="btn btn-primary" href="add_journal.php" role="button">Add New Entry</a>
     </div>

     <?php
     if (isset($message)) {
         echo '<div class="alert alert-'.$message['type'].' mx-auto text-center">'.$message['text'].'</div>';
     }
     ?>

     <?php
     if (isset($journal_headers, $journal_table)):
    ?>
    <div class="row">
      <div class="col-12">
         <table class="table table-striped table-hover" id="journal_table">
             <thead>
                 <?=$journal_headers?>
             </thead>
             <tbody>
             <?=$journal_table?>
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
        var table = $('#journal_table').DataTable({
        });
        table.buttons().container().appendTo('#journal_table_wrapper .col-md-6:eq(0)');
    });


    function delete_journal(id) {
        let confirmation = confirm('Are you sure you want to delete this entry?');

        if (confirmation) {
            $.ajax({
                method: "POST",
                url: "ajax.php",
                data: { action: "delete_log", journal_id_to_delete: id }
            })
                .done(function( msg ) {
                    if (msg == 'yay') {
                        let table = $('#journal_table').DataTable();
                        let selector = 'journal_row_'+id;
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
