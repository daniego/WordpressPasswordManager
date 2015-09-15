<?php
function passwordmanager_list_password(){
    global $wpdb;
    include 'function.php';
    ?> 
    <table>
    <?php
    $passgroups = $wpdb->get_results(" SELECT * FROM " . TABLE_GRP . " ");
    if($passgroups){
        //Passwordgroup loop
        foreach ($passgroups as $passgroup){
            //check if the user in a group member or is allowed for a single password
            $user = wp_get_current_user();
            if(check_user_pass_4group($passgroup->id,$user->ID) === 'ENABLE'){
                ?>
                <tr>
                    <td colspan="7"><?php echo $passgroup->name;?></td>
                </tr>
                
                <tr>
                    <td>ID</td>
                    <td>Name</td>
                    <td>Description</td>
                    <td>Type</td>
                    <td>Link</td>
                    <td>Username</td>
                    <td>Password</td>
                </tr>
                <?php
                $usergrp = $wpdb->get_results();
                
                //$passwords = $wpdb->get_results("SELECT * FROM " . TABLE_PASS . " WHERE id_grp LIKE ".$passgroup->id." ");
                //query for password assigned to user
                $passwords = $wpdb->get_results("
                            SELECT DISTINCT * FROM " . TABLE_PASS . " password
                            INNER JOIN " . TABLE_ASS . " ass
                            ON password.id_pass = ass.pass_id
                            WHERE 
                            (
                            password.id_grp LIKE ".$passgroup->id."
                            AND ass.type = 'password'
                            AND ass.user_id = ".$user->ID."
                            AND ass.usergroup_id = 'NULL'
                            )
                            OR
                            (
                            password.id_grp LIKE ".$passgroup->id." 
                            
                            AND ass.usergroup_id = '2'
                            )
                            "
                            );
                if ($passwords) {
                    
                    foreach ($passwords as $password) {
                        ?>
                        <tr id="passid-<?php echo $password->id_pass;?>">
                            <td><?php echo $password->id_pass;?></td>
                            <td><?php echo $password->name;?></td>
                            <td><?php echo $password->description;?></td>
                            <td><?php retrieve_gruopname($password->id_type);?></td>
                            <td><?php echo $password->host;?></td>
                            <td><?php echo $password->user;?></td>
                            <td><?php echo $password->pass;?></td>
                        </tr>
                    <?php
                    }   
                }
            }
        }//Passwordgroup loop END
    }
    
    ?>
    </table>
    <?php
}

function passwordmanager_add_password(){
    
}