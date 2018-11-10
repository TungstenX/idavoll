<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rows = $db_func->getAllCapacityItems();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_is_main_capacity = false;
		$selected_capacity_type = "";
		$selected_max = "";
		$selected_price_factor = 1.0;
	?>	
	<h2 class="nav-tab-wrapper"><?php _e('Capacity Items', $this->plugin_name); ?></h2>
	<p>This is to set up capacity items / classes to be used as part of the configuration of room types.</p>
	<h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?></h3>
	<form method="post" name="booking_capacity" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="cap_in" />
		<table>
    		<tr>    			
        		<!-- Is Main capacity -->
        		<td><span><?php esc_attr_e('Is main capacity: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Is main capacity: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-capacity-main_capacity">			                
			                <input type="checkbox" <?php if($selected_is_main_capacity) {echo "checked";} ?>' id="<?php echo $this->plugin_name; ?>-capacity-main_capacity" name="<?php echo $this->plugin_name; ?>-capacity[main_capacity]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Capacity type / name -->
        		<td><span><?php esc_attr_e('Capacity type / name: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Capacity type / name: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-capacity-type">			                
			                <input type="text" value='<?php if(!empty($selected_capacity_type)) {echo $selected_capacity_type;} ?>' placeholder='Capacity type / name'  id="<?php echo $this->plugin_name; ?>-capacity-type" name="<?php echo $this->plugin_name; ?>-capacity[capacity_type]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Capacity max -->
        		<td><span><?php esc_attr_e('Maximum: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Maximum: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-capacity-max">			                
			                <input type="number" step="1" value='<?php if(!empty($selected_max)) {echo $selected_max;} ?>' placeholder='2'  id="<?php echo $this->plugin_name; ?>-capacity-max" name="<?php echo $this->plugin_name; ?>-capacity[capacity_max]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Capacity factor -->
        		<td><span><?php esc_attr_e('Price multiplication factor: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Maximum: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-capacity-factor">			                
			                <input type="number" step="0.01" value='<?php if(!empty($selected_price_factor)) {echo factorToPercentage($selected_price_factor);} ?>' placeholder='100.00'  id="<?php echo $this->plugin_name; ?>-capacity-factor" name="<?php echo $this->plugin_name; ?>-capacity[capacity_factor]" /> %
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>
				<td colspan="2"><?php submit_button(__('Save capacity item', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>

	<!-- List of: -->
	<h2 class="nav-tab-wrapper"><?php _e('Capacity items', $this->plugin_name); ?></h2>
	<table border="1">
	<?php 
	if(is_null($rows) || count($rows) == 0) {
		?>
		<tr><td>None</td></tr>
		<?php
	} else {
		?>
		<tr>
			<th>Type / name</th>
			<th>Is main</th>
			<th>Maximum</th>
			<th>Factor</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rows as $row) {
		?>
		<tr>
			<td><?php echo $row->capacity_type; ?></td>
			<td><?php echo $row->main_capacity == 1 ? "Yes" : ""; ?></td>
			<td><?php echo $row->max; ?></td>
			<td><?php echo factorToPercentage($row->price_factor); ?> %</td>
			<td><button><?php echo $row->id; ?></button></td>
		</tr>
		<?php
		}
	}
	?>
	</table>