<?php

/**
 * Provide a admin area view for the plugin
 *
 * This file is used to markup the admin-facing aspects of the plugin.
 *
 * @link       https://ParanoidAndroid.co.za
 * @since      1.0.0
 *
 * @package    Idavoll
 * @subpackage Idavoll/admin/partials
 */
?>

<!-- This file should primarily consist of HTML with a little bit of PHP. -->
<div class="wrap">

    <h2 class="nav-tab-wrapper"><?php _e('Booking Options', $this->plugin_name); ?></h2>
    
    <form method="post" name="booking_options" action="options.php">
    	<?php 
    	settings_fields($this->plugin_name); 
    	//Grab all options
        $options = get_option($this->plugin_name);

        // Booking options
        $deposit = $options['deposit'];
        $cancellationfee = $options['cancellationfee'];
        $cancellationdays = $options['cancellationdays'];
        $background_colour = $options['background_colour'];
        settings_fields($this->plugin_name);
        do_settings_sections($this->plugin_name);
    	?>
    	<table>
    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('Deposit and cancellation options', $this->plugin_name); ?><h3></td>
        	</tr>
    		<tr>    			
        		<!-- Deposit required at booking -->
        		<td><span><?php esc_attr_e('Deposit required: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Deposit required: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-deposit">			                
			                <input type="number" step='0.01' value='<?php if(!empty($deposit)) {echo $deposit;} else {echo "50.00";} ?>' placeholder='50.00' id="<?php echo $this->plugin_name; ?>-deposit" name="<?php echo $this->plugin_name; ?>[deposit]" /> %
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>
        		<!-- Cancellation fee -->
        		<td><span><?php esc_attr_e('Cancellation fee: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Cancellation fee: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-cancellationfee">			            
			                <input type="number" step='0.01' value='<?php if(!empty($cancellationfee)) {echo $cancellationfee;} else {echo "50.00";} ?>' placeholder='50.00' id="<?php echo $this->plugin_name; ?>-cancellationfee" name="<?php echo $this->plugin_name; ?>[cancellationfee]" /> %
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>
        		<!-- Cancellation days -->
        		<td><span><?php esc_attr_e('Cancellation kick-in: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Cancellation kick-in: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-cancellationdays">        	
			                <input type="number" step='1' value='<?php if(!empty($cancellationdays)) {echo $cancellationdays;} else {echo "14";} ?>' placeholder='14'  id="<?php echo $this->plugin_name; ?>-cancellationdays" name="<?php echo $this->plugin_name; ?>[cancellationdays]" value="1" /> days                
			            </label>
			        </fieldset>
			    </td>
			</tr>

    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('Styling options', $this->plugin_name); ?><h3></td>
        	</tr>
        	<tr>
        		<td><span><?php esc_attr_e('Background colour: ', $this->plugin_name); ?></span></td>
        		<td>
        			<!-- background colour-->
		            <fieldset class="idavoll-admin-colours">
		                <legend class="screen-reader-text"><span><?php _e('Background colour: ', $this->plugin_name);?></span></legend>
		                <label for="<?php echo $this->plugin_name;?>-background_colour">
		                    <input type="text" class="<?php echo $this->plugin_name;?>-colour-picker" id="<?php echo $this->plugin_name;?>-background_colour" name="<?php echo $this->plugin_name;?>[background_colour]"  value="<?php echo $background_colour;?>"  />		                    
		                </label>
		            </fieldset>
		        </td>
        	</tr>

			<tr>
				<td colspan="2"><?php submit_button(__('Save all changes', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
    </form>

</div>
