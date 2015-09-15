<?php

function passwordmanager_manage_passwordtype() {
    add_action( 'admin_init', 'my_plugin_admin_init' );
    global $wpdb;
    echo '<pre>' . print_r($_POST) . '</pre>';

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    //add group
    if(isset($_POST['action'])){
        if ($_POST['action'] === 'Add') {
            $name = $_POST['grouppass'];
            echo "User Has submitted the form to ADD a group : <b> $name </b>";

            //CHECK DUPLICATE
            $check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_TYPE . " WHERE name = '$name'"));
            if ($check_duplicate === 1) {
                require 'messages.php';
                alert_duplicate('group', 'duplicate', $name);
            } else {
                $insert_query = $wpdb->query($wpdb->prepare("INSERT INTO " . TABLE_TYPE . " ( name ) VALUES ( %s)", $name));
            }
        }
    }

    //update group
    if(isset($_POST['action'])){
        if ($_POST['action'] === 'Update') {
            $oldvalue = $_POST['oldvalue'];
            $newvalue = $_POST['newvalue'];

            //CHECK DUPLICATE
            if($newvalue === ''){
                require 'messages.php';
                alert_duplicate('group', 'empty', $oldvalue);
            }
            //$check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_TYPE . " WHERE name = '$newvalue'"));
            $check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_TYPE . " WHERE name = %s ", $newvalue));
            if ($check_duplicate === 1) {
                require 'messages.php';
                alert_duplicate('group', 'duplicate', $oldvalue);
            } else {
                $wpdb->update(
                        TABLE_TYPE, array('name' => $newvalue), array('name' => $oldvalue), array('%s')
                );
                echo "Value update successfully";
            }
        }
    }
    
    //delete group
    if(isset($_POST['Delete'])){
        if ($_POST['Delete'] === 'Delete') {
            $oldvalue = $_POST['oldvalue'];

            $wpdb->update(
                    TABLE_TYPE, array('trash' => '1'), array('name' => $oldvalue), array('%d')
            );
        }
    }


    echo '<div class="wrap">';
    echo '<h1>Type Manager</h1>';
    echo '</div>';
    ?>
    <hr>
    <h2>Add group</h2>
    <form name="group-add" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3>Name</h3>
        <input type="text" name="grouppass" required="required"/>
    <?php submit_button('Add', 'primary', 'action'); ?>


    </form>

    <hr>
    <h2>Edit or Delete group</h2>
    <form name="group-edit" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
        <select name="oldvalue" id="mySelect" onchange="selectionchange();">
            <option value="">---</option>
    <?php
    $grouplists = $wpdb->get_results(" SELECT * FROM " . TABLE_TYPE . " ");
    if ($grouplists) {
        foreach ($grouplists as $grouplist) {
            //echo $grouplist->name."<br>";
            echo "<option value=\"$grouplist->name\">$grouplist->name</oprion>";
        }
    }
    ?>
        </select>
        
        <input type="text" name="newvalue" id="txt" required="required" />
        <script type="text/javascript">
            function selectionchange()
                {
                    var e = document.getElementById("mySelect");
                    var str = e.options[e.selectedIndex].value;
                    document.getElementById('txt').value = str;
                }
        </script>
        <?php submit_button('Update', 'primary', 'action'); ?>
        <input type="submit" value="Delete" name="Delete" onclick="realydelete()" class="button-secondary delete"\>
        <?php //submit_button('Delete', 'delete', 'action'); ?>
        
    </form>
    <script type="text/javascript">
        function realydelete() {
            if (confirm('Are you sure you want to save this thing into the database?')) {
                // Save it!
            } else {
                // Do nothing!
            }
        }
    </script>
    <hr>


    <?php
    echo "FINE";
}
?>
