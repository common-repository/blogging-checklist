<?php
/*
Plugin Name: Blogging Checklist
Plugin URI: http://www.robbyslaughter.com/
Description: Lets you create a static checklist that appears on your posts page as a remidner
Version: 1.0
Author: Slaughter Development
Author URI: http://www.slaughterdevelopment.com/
*/

// Create the function to output the contents of our Dashboard Widget

/* Use the admin_menu action to define the custom boxes */
add_action('admin_menu', 'bloggingchecklist_add_custom_box');

/* Adds a custom section to the "advanced" Post and Page edit screens */
function bloggingchecklist_add_custom_box() {

  add_options_page('Blogging Checklist Options', 'Blogging Checklist', 'manage_options', 'bloggingchecklist', 'bloggingchecklist_options');

   
  if( function_exists( 'add_meta_box' )) {
    add_meta_box( 'bloggingchecklist_sectionid', __( 'Blogging Checklist', 'bloggingchecklist_textdomain' ), 
                'bloggingchecklist_inner_custom_box', 'post', 'normal', 'high' );
    add_meta_box( 'bloggingchecklist_sectionid', __( 'Blogging Checklist', 'bloggingchecklist_textdomain' ), 
                'bloggingchecklist_inner_custom_box', 'page', 'normal', 'high' );
   } else {
    add_action('dbx_post_advanced', 'bloggingchecklist_old_custom_box' );
    add_action('dbx_page_advanced', 'bloggingchecklist_old_custom_box' );
  }
}
   
function bloggingchecklist_options() {

  if (!current_user_can('manage_options'))  {
    wp_die( __('You do not have sufficient permissions to access this page.') );
  }

  
  // variables for the field and option names 
  $opt_name = 'bloggingchecklist_content';
  $hidden_field_name = 'bloggingchecklist_submit_hidden';
  $data_field_name = 'bloggingchecklist_content';

  // Read in existing option value from database
  $opt_val = get_option($opt_name);

  // See if the user has posted us some information
  // If they did, this hidden field will be set to 'Y'
  if( isset($_POST[ $hidden_field_name ]) && $_POST[ $hidden_field_name ] == 'Y' ) {
    // Read their posted value
    $opt_val = stripslashes($_POST[ $data_field_name ]);

    // Save the posted value in the database
    update_option($opt_name, $opt_val);

    // Put an settings updated message on the screen
    ?>
      <div class="updated"><p><strong><?php _e('Settings saved.', 'menu-test' ); ?></strong></p></div>
    <?php
   }

   // Now display the settings editing screen
   $opt_val_converted = blogging_checklist_convert($opt_val);
   
   echo '<div class="wrap">';
   // header
   echo "<h2>" . __( 'Blogging Checklist Plugin Settings', 'blogging-checklist' ) . "</h2>";
   
   ?>
     <p>
       The Blogging Checklist is an extremely simple tool to add a checklist to your Edit Post page.
     </p>
       
     <p>You can make checkboxes using brackets <strong>[ ]</strong> or simple HTML. Note that the
       status of the checkboxes is <em>always reset</em> whenever you save your post.
     </p> 
      
     <p>Happy checklisting!</p>
   <?  
   
   // settings form
   ?>
   <form name="form1" method="post" action="">
      <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">
      
      <p><?php _e("Checklist content:", 'blogging-checklist' ); ?> <em>Use simple HTML, or make a checkbox on each line with </em><code>[ ] item</code>. <em>Examples below.</em><br />
      <textarea name="<?php echo $data_field_name; ?>" style="width:100%; height:200px"><?php echo $opt_val; ?></textarea>
            </p>
      <p class="submit">
      <input type="submit" name="Submit" class="button-primary" value="<?php esc_attr_e('Save Changes') ?>" />
      </p>
      <hr />
      Your Checklist:
      <div style="background:#ffffff; border:1px solid #cccccc; margin:10px; padding:10px">
         <?php echo $opt_val_converted; ?>
      </div>
      
      Examples to try <em>(copy and paste!)</em>:
      <div style="background:#f3f3f3; border:1px solid #cccccc; margin:10px; padding:10px; font-family:Consolas, Courier">
         [ ] Produce draft <br />
         [ ] Check title for SEO <br />
         [ ] Add at least five keywords <br />
         [ ] Check at least two categories <br />
         [ ] Schedule post <br />
         [ ] Send to someone else for review <br /><br /><br /><br />
         
         &lt;b&gt;Writing&lt;/b&gt;<br />
         [ ] Review style guide document<br />
         [ ] Find an image from our &lt;a href="http://openphoto.net/">stock photo site&lt;/a&gt;<br />
         &lt;b&gt;Editing&lt;/b&gt;<br />
         [ ] Read the blog post out loud<br />
         [ ] [ ] Check your spelling twice!<br />
         &lt;b&gt;Pre-Flight&lt;/b&gt;<br />
         [ ] Preview in Internet Explorer<br /> 
         [ ] Preview in Mozilla Firefox<br />
         [ ] Post!
      </div>
      
   </form>
   </div>
<?  
}

/* Prints the inner fields for the custom post/page section */
function bloggingchecklist_inner_custom_box() {

  // Use nonce for verification

  echo '<input type="hidden" name="bloggingchecklist_noncename" id="bloggingchecklist_noncename" value="' . 
    wp_create_nonce( plugin_basename(__FILE__) ) . '" />';

  // The actual fields for data entry
  ?>
  <em>Note: the following checkboxes will be cleared each time you load this page and are not interactive.</em>
  <?

  // Read in existing option value from database, and display
  echo "<p>";
  echo blogging_checklist_convert(get_option("bloggingchecklist_content"));
  echo "</p>";
    
}

function blogging_checklist_convert($input)
{
   if (strpos($input,"[ ]") !== false)
   {
     $output = "<form>" . nl2br(preg_replace("/\[ \]/",
       "<input type=" . '"' . "checkbox" . '"' . ">",$input)) . "</form>";
   }
   else
   {
     $output = $input;
   }
   
   return $output;
}

/* Prints the edit form for pre-WordPress 2.5 post/page */
function bloggingchecklist_old_custom_box() {

  echo '<div class="dbx-b-ox-wrapper">' . "\n";
  echo '<fieldset id="bloggingchecklist_fieldsetid" class="dbx-box">' . "\n";
  echo '<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle">' . 
        __( 'Blogging Checklist', 'bloggingchecklist_textdomain' ) . "</h3></div>";   
   
  echo '<div class="dbx-c-ontent-wrapper"><div class="dbx-content">';

  // output editing form

  bloggingchecklist_inner_custom_box();

  // end wrapper

  echo "</div></div></fieldset></div>\n";
}


?>