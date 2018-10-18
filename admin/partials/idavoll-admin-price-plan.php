<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rows = $db_func->getAllPricePlans();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_base_amount = 0.0;
		$selected_single_factor = 1.0;
		$selected_price_type = 1;
	?>
	<h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?></h3>
	<form method="post" name="booking_price_plan" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="price_plan_in" />
		<table>
    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('Price Plans', $this->plugin_name); ?></h3></td>
        	</tr>
        	<tr>    			
        		<!-- Price type -->
        		<td><span><?php esc_attr_e('Price type: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Price type: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-price_type">			                
			            	<select id="<?php echo $this->plugin_name; ?>-price-plan-price_type" name="<?php echo $this->plugin_name; ?>-price-plan[price_type]">
			            		<option value="0"><?php _e('Per room', $this->plugin_name); ?></option>
			            		<option value="1"><?php _e('Per person sharing', $this->plugin_name); ?></option>
			            	</select>
			            </label>
			        </fieldset>
			    </td>
			</tr>
    		<tr>    			
        		<!-- Base amount -->
        		<td><span><?php esc_attr_e('Base amount: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Base amount: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-base_amount">			                
			                <input type="number" step="1.00" <?php if($selected_base_amount) {echo $selected_base_amount;} ?>' id="<?php echo $this->plugin_name; ?>-price-plan-base_amount" name="<?php echo $this->plugin_name; ?>-price-plan[base_amount]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Single factor -->
        		<td><span><?php esc_attr_e('Single factor: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Single factor: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-single_factor">			                
			                <input type="number" step="0.1" value='<?php if(!empty($selected_single_factor)) {echo $selected_single_factor;} ?>'  id="<?php echo $this->plugin_name; ?>-price-plan-single_factor" name="<?php echo $this->plugin_name; ?>-price-plan[single_factor]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>
				<td colspan="2"><?php submit_button(__('Save all changes', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>

	<!-- List of: -->
	<h3 class="nav-tab-wrapper"><?php _e('Price plans', $this->plugin_name); ?></h3>
	<table border="1">
	<?php 
	if(is_null($rows) || count($rows) == 0) {
		?>
		<tr><td>None</td></tr>
		<?php
	} else {
		?>
		<tr>
			<th>Type</th>
			<th>Base amount</th>
			<th>Single factor</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rows as $row) {
		?>
		<tr>
			<td><?php if($row->price_type == 0) { echo "Per room"; } else if ($row->price_type == 1) { echo "Per single sharing"; } ?></td>
			<td><?php echo $row->base_amount; ?></td>
			<td><?php echo $row->single_factor; ?></td>
			<td><button><?php echo $row->id; ?></button></td>
		</tr>
		<?php
		}
	}
	?>
	</table>