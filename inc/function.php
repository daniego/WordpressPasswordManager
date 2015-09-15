<?php
function passwordmanager_associate_user_group(){
    //echo TABLE_USERGRP;
    global $wpdb;
    ?>
    <form name="group-edit" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>" method="POST">
        <select name="mySelectgroup" id="mySelectgroup" onChange="selectionchangeuser(this.value);">
            <option value="">---</option>
    <?php
    $grouplists = $wpdb->get_results(" SELECT * FROM " . TABLE_USERGRP . " WHERE trash LIKE '0'");
    if ($grouplists) {
        foreach ($grouplists as $grouplist) {
            //echo $grouplist->name."<br>";
            echo '<option value="'.$grouplist->id.'">'.$grouplist->name.'</oprion>';
        }
    }
    ?>
        </select>
        <div id="users-list"><p>Please select a group</p></div>
    </form>
    <script>
        function selectionchangeuser(usergroup) {
            var natura = document.getElementById('mySelectgroup');
            naturaValue = natura.value;
            //alert(usergroup);
            jQuery.ajax({
                       type: "POST",
                       url: "/wp-content/plugins/password-manager/inc/function.php",
                       data: "usergrp_ID=" + usergroup,
                       success: function(response){
                            //alert('ok-'+response);
                            document.getElementById("users-list").innerHTML = response;
                        },
                        error: function (request, status, error) {
                            alert(error);
                            alert(request.responseText);
                        }  
                     });
        }
    </script>
    <?php
}

if(isset($_POST['usergrp_ID'])){
     GetTotalPL($_POST['usergrp_ID']);
}

 function GetTotalPL($id){
    require_once( "../../../../wp-config.php" );
    global $wpdb;
    
    echo '<form action="'.str_replace('%7E', '~', $_SERVER['REQUEST_URI']).'"><fieldset><legend>Users</legend><br>';
    $blogusers = get_users( 'orderby=nicename' );
    $count = 0;
    foreach ( $blogusers as $user ) {
        $grouplists = $wpdb->get_results(" SELECT * FROM " . TABLE_ASS . " WHERE type LIKE 'usergroup' AND user_id like '".$user->ID."'AND usergroup_id LIKE '".$id."'");
        
        //print_r($grouplists);
        $checkbox = '';
        $count ++;
        if(!empty($grouplists)) $checkbox = 'checked';
        //echo '<input type="checkbox" name="user'.$count.'" value="'.$user->ID.'" '.$checkbox.'/>'
        echo '<input type="checkbox" name="user[]" value="'.$user->ID.'" '.$checkbox.'/>'
                    .get_user_meta( $user->ID, 'first_name', 'true' ).' '.get_user_meta( $user->ID, 'last_name', 'true' ).'<br>';
    }
    
     echo '</fieldset></form>';
     echo '<input type="submit" value="Update" name="updategrp" class="button button-primary"\>';
     //submit_button('Update', 'primary', 'action');
}

function check_user_pass_4group($passwordgroup,$user){
    global $wpdb;
    
    //serialize passwords for password group
    $passwords = $wpdb->get_results("SELECT id_pass FROM " . TABLE_PASS . " WHERE id_grp = '$passwordgroup' ");
    if($passwords){
        foreach ($passwords as $password) {
            $singlepass[] = $password->id_pass;
        }
    }
    
    //get usergroups for the user
    $usergroups = $wpdb->get_results("SELECT usergroup_id FROM " . TABLE_ASS . " WHERE type = 'usergroup' AND user_id LIKE '".$user."'  ");
    
    if($usergroups){
        foreach($usergroups as $usergroup) {
            //check if a password for this group is assigned to a user group
            if($passwords){
                foreach ($singlepass as $value) {
                    $query_sigle = $wpdb->get_var("SELECT COUNT(*) FROM ".TABLE_ASS." WHERE type = 'password' AND usergroup_id = '$usergroup->usergroup_id' AND pass_id = '$value' ");
                    if($query_sigle >= 1){ return 'ENABLE';break;}
                }
            }
            
            
        }
    }
    
    //check if a password for this group is assigned to a user
    if($passwords){
        foreach ($singlepass as $value) {
            $query_sigle = $wpdb->get_var("SELECT COUNT(*) FROM ".TABLE_ASS." WHERE type = 'password' AND user_id = '$user' AND pass_id = '$value' ");
            if($query_sigle >= 1){ return 'ENABLE';break;}
        }
    }
}

function retrieve_gruopname($id){
    global $wpdb;
    $passwords = $wpdb->get_results(" SELECT * FROM " . TABLE_PASS . " ");
    $gruop = $wpdb->get_var( "SELECT name FROM " . TABLE_TYPE . "" );
    echo $gruop;
}