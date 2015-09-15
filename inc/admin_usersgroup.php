<?php

function passwordmanager_manage_usersgroup() {
    add_action( 'admin_init', 'my_plugin_admin_init' );
    global $wpdb;
    echo 'aaa<pre>'; print_r($_POST);echo '</pre>';

    if (!current_user_can('manage_options')) {
        wp_die(__('You do not have sufficient permissions to access this page.'));
    }

    //add group
    if(isset($_POST['action'])){
        if ($_POST['action'] === 'Add') {
            $name = $_POST['grouppass'];
            echo "User Has submitted the form to ADD a usergroup : <b> $name </b>";
            //echo TABLE_NAME1;

            //CHECK DUPLICATE
            $check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_USERGRP . " WHERE name = '$name'"));
            if ($check_duplicate === 1) {
                require 'messages.php';
                alert_duplicate('group', 'duplicate', $name);
            } else {
                $insert_query = $wpdb->query($wpdb->prepare("INSERT INTO " . TABLE_USERGRP . " ( name, trash ) VALUES ( %s, %d )", $name, 0));
            }
            //
            //$insert_query = $wpdb->insert(TABLE_USERGRP, array('name' => $name));
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
            //$check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_USERGRP . " WHERE name = '$newvalue'"));
            $check_duplicate = $wpdb->query($wpdb->prepare("SELECT * FROM " . TABLE_USERGRP . " WHERE name = %s ",$newvalue));
            if ($check_duplicate === 1) {
                require 'messages.php';
                alert_duplicate('group', 'duplicate', $oldvalue);
            } else {
                $wpdb->update(
                        TABLE_USERGRP, array('name' => $newvalue), array('name' => $oldvalue), array('%s')
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
                    TABLE_USERGRP, array('trash' => '1'), array('name' => $oldvalue), array('%d')
            );

        }
    }
    
    //Associoation update
    if(isset($_POST['updategrp'])){
        if($_POST['updategrp'] === 'Update'){
            if(isset($_POST['mySelectgroup'])) $mySelectgroup = $_POST['mySelectgroup'];
            echo "association update";
            //DELETE previusly isered data
            $wpdb->query( 
                    $wpdb->prepare( 
                            "
                            DELETE FROM ".TABLE_ASS."
                             WHERE type = %s
                             AND usergroup_id = %s
                            ",
                            'usergroup', $mySelectgroup
                    )
            );
            if(isset($_POST['user'])){
                
            
                if (is_array($_POST['user'])) {
                    foreach($_POST['user'] as $value){
                      echo $value;
                      //Insert value in DB
                      $insert_query = $wpdb->query($wpdb->prepare("INSERT INTO " . TABLE_ASS . " ( type, user_id, usergroup_id ) VALUES ( %s, %d, %d )", 'usergroup', $value, $mySelectgroup));
                    }
                }
            }
        }
    }

    echo '<div class="wrap">';
    echo '<h1>Users Group Manager</h1>';
    echo '</div>';
    //echo $_SERVER['PHP_SELF'];
    //echo $_POST['grouppass'];
    ?>
    <hr>
    <h2>Add group</h2>
    <form name="group-add" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
        <h3>Name</h3>
        <input type="text" name="grouppass" required="required"/>
    <?php submit_button('Add', 'primary', 'action'); ?>


    </form>

    <hr>
    <h2>Edit or Delete users group</h2>
    <form name="group-edit" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
        <select name="oldvalue" id="mySelect" onchange="selectionchange();">
            <option value="">---</option>
    <?php
    $grouplists = $wpdb->get_results(" SELECT * FROM " . TABLE_USERGRP . " WHERE trash LIKE '0'");
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

    <h2>Associate user to group</h2>
    <?php
    require 'function.php';
    //require_once ''; '../function.php';
    passwordmanager_associate_user_group();
}
