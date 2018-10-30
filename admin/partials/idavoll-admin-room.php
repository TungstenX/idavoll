<?php
/**
* TODO: 
* 	- All edits after saved button functionality
*   - All remove after saved button functionality
*	- Remove price plan item from price plan after it is saved
*/
?>
	<p>Description blah</p>

	<!-- Price plan items -->
	<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rowsTypes = $db_func->getAllRoomTypes();
		$rowsCapacityItems = $db_func->getAllCapacityItems();
		
		$selected_type_name = "";
		$selected_capacity_item = 0;
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Room types', $this->plugin_name); ?></h2>
	<form method="post" name="booking_room_type" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="room_type_in" />
		<table>
    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?></h3></td>
        	</tr>
        	<tr>    			
        		<!-- name -->
        		<td><span><?php esc_attr_e('Name: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Name: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-type-type_name">			                
			                <input type="text" value="<?php if($selected_room_type) {echo $selected_room_type;} ?>" id="<?php echo $this->plugin_name; ?>-room-type-type_name" name="<?php echo $this->plugin_name; ?>-room-type[type_name]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
        	<tr>    			
        		<!-- main capacity -->
        		<td><span><?php esc_attr_e('Main capacity item: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Main capacity item: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-type-main_capacity_item">			                
			            	<select id="<?php echo $this->plugin_name; ?>-room-type-main_capacity_item" name="<?php echo $this->plugin_name; ?>-room-type[main_capacity_item]">
			            		<?php 
			            		if (!is_null($rowsCapacityItems) && (count($rowsCapacityItems) > 0)) {
				            		foreach ($rowsCapacityItems as $key => $value) {			            			
				            			if($value->main_capacity) {
					            			echo "<option value=\"{$value->id}\" ";
					            			if($selected_capacity_item == $value->id) { 
					            				echo "selected "; 
					            			}
					            			echo ">" . __($value->capacity_type, $this->plugin_name) . "</option>";
					            		}
				            		}
			            		}
			            		?>
			            	</select>
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Additional -->
        		<td><span><?php esc_attr_e('Additional capacity items: ', $this->plugin_name); ?></span></td>
        		<td>
        			<select id="tmp_add_cap_items" name="tmp_add_cap_items">
        				<?php 
        				if (!is_null($rowsCapacityItems) && (count($rowsCapacityItems) > 0)) {
	        				foreach ($rowsCapacityItems as $key => $value) {
	        					if(!$value->main_capacity) {
	        						echo "<option value=\"{$value->id}\" >" . __($value->capacity_type, $this->plugin_name) . " | " . $value->max . " | " . $value->factor . "</option>";
			            		}
	        				}
	        			}
        				?>
        			</select>
        			<script type="text/javascript">
        				var tmp_add_cap_item_counter = 0;
						// tmp_price_plan_item_button
						//Defining a listener for our button, specifically, an onclick handler
						function add_tmp_add_cap_item() {
						    //First things first, we need our text:
							var e = document.getElementById("tmp_add_cap_items");
							var id = e.options[e.selectedIndex].value;
							var text = e.options[e.selectedIndex].text;
							var hidden = "<input type=\"hidden\" value=\"" + id + "\" name=\"idavoll-room-type[add_cap][" + tmp_add_cap_item_counter + "]\"" + "id=\"idavoll-room-type-add_cap-" + tmp_add_cap_item_counter + "\" />"
						    //Now construct a quick list element
							var elLI = document.createElement('li');
							elLI.innerHTML = text + " <button onclick=\"remove_tmp_add_cap_item('" + tmp_add_cap_item_counter + "')\">-</button>" + hidden;
							elLI.setAttribute("id", "idavoll-room-type-add_cap-li-" + tmp_add_cap_item_counter);

						    //Now use appendChild and add it to the list!
						    document.getElementById("tmp_add_cap_item_list").appendChild(elLI);
						    tmp_add_cap_item_counter++;
						}

						function remove_tmp_add_cap_item(counter) {
							var item = document.getElementById("idavoll-room-type-add_cap-li-" + counter);
							item.parentNode.removeChild(item);
						}
        			</script>
        			<button id="tmp_add_cap_item_button" onclick="add_tmp_add_cap_item();return false;">+</button><br />
        			<ul id="tmp_add_cap_item_list">
        			</ul>
			    </td>
			</tr>    		
			<tr>
				<td colspan="2"><?php submit_button(__('Save all changes', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>

	<!-- List of: -->
	<h3 class="nav-tab-wrapper"><?php _e('Room types', $this->plugin_name); ?></h3>
	<table border="1">
	<?php 

	if(is_null($rowsTypes) || count($rowsTypes) == 0) {
		?>
		<tr><td>None</td></tr>
		<?php
	} else {
		?>
		<tr>
			<th>Name</th>
			<th>Main capacity</th>
			<th>Additional capacity</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rowsTypes as $rowsType) {
			// error_log("Room page: " . print_r($rowsType, 1) , 0);
			$main_cap = $db_func->getCapacityItem($rowsType->id_room_capacity_item);
			$add_caps = $db_func->getCapacityItemByRoomTypes($rowsType->id);
		?>
		<tr>
			<td><?php echo $rowsType->type_name; ?></td>
			<td><?php 
				if($main_cap) {
					echo __($main_cap->capacity_type, $this->plugin_name) . " | " . $main_cap->max . " | " . factorToPercentage($main_cap->price_factor);
				} else {
					echo "&nbsp;";
				} 
			?></td>
			<td><?php
				if($add_caps) {
					foreach ($add_caps as $key => $value) {
						echo __($value->capacity_type, $this->plugin_name) . " | " . $value->max . " | " . factorToPercentage($value->price_factor) . "<br />";	
					}
				} else {
					echo "&nbsp;";
				}
			?></td>
			<td><button><?php echo $rowsType->id; ?></button></td>
		</tr>
		<?php
		}
	}
	?>
	</table>
	<br />
	<br />
	<!-- Rooms -->
	<?php 
		$rows = $db_func->getAllRooms();		
		$rowsRoomTypes = $db_func->getAllRoomTypes();
		$rowsPricePlans = $db_func->getAllPricePlans();		
		$selected_room_name = "";
		$selected_room_description = "";
		$selected_id_room_type = 0;
		$selected_id_room_composite = 0;
		$selected_id_price_plan = 0;
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Rooms', $this->plugin_name); ?></h2>
	<h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?></h3>
	<form method="post" name="booking_room" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="room_in" />
		<table>
    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('Rooms', $this->plugin_name); ?></h3></td>
        	</tr>
        	<tr>    			
        		<!-- Room name -->
        		<td><span><?php esc_attr_e('Room name: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room name: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-room_name">			                
			                <input type="text" value="<?php if($selected_room_name) {echo $selected_room_name;} ?>" id="<?php echo $this->plugin_name; ?>-room-room_name" name="<?php echo $this->plugin_name; ?>-room[room_name]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
        	<tr>    			
        		<!-- Room description -->
        		<td><span><?php esc_attr_e('Room description: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room description: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-room_description">			                
			                <input type="text" value="<?php if($selected_room_description) {echo $selected_room_description;} ?>" id="<?php echo $this->plugin_name; ?>-room-room_description" name="<?php echo $this->plugin_name; ?>-room[room_description]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>

        	<tr>    			
        		<!-- Room type -->
        		<td><span><?php esc_attr_e('Room type: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room type: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-room_type">			                
			            	<select id="<?php echo $this->plugin_name; ?>-room-room_type" name="<?php echo $this->plugin_name; ?>-room[room_type]">
			            		<?php 

			            		if (!is_null($rowsRoomTypes) && (count($rowsRoomTypes) > 0)) {
				            		foreach ($rowsRoomTypes as $key => $value) {			            			
				            			echo "<option value=\"{$value->id}\" ";
				            			if($selected_id_room_type == $value->id) { 
				            				echo "selected "; 
				            			}
			            				echo ">" . __($value->type_name, $this->plugin_name) . "</option>";
				            		}
			            		}
			            		?>
			            	</select>
			            </label>
			        </fieldset>
			    </td>
			</tr>
    		<tr>    			
        		<!-- Price plan -->
        		<td><span><?php esc_attr_e('Price plan: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Price plan: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-price_plan">			                
			            	<select id="<?php echo $this->plugin_name; ?>-room-price_plan" name="<?php echo $this->plugin_name; ?>-room[price_plan]">
			            		<?php 
			            		if (!is_null($rowsPricePlans) && (count($rowsPricePlans) > 0)) {
				            		foreach ($rowsPricePlans as $key => $value) {			            			
				            			echo "<option value=\"{$value->id}\" ";
				            			if($selected_id_price_plan == $value->id) { 
				            				echo "selected "; 
				            			}
				            			echo ">";
				            			if($value->price_type == 0) { 
											echo __("Per room", $this->plugin_name); 
										} else if ($value->price_type == 1) { 
											echo __("Per single sharing", $this->plugin_name); 
										}
										echo " | " . $value->base_amount . " | " . factorToPercentage($value->single_factor) . "%</option>";
				            		}
			            		}
			            		?>
			            	</select>
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
	<h3 class="nav-tab-wrapper"><?php _e('Rooms', $this->plugin_name); ?></h3>
	<table border="1">
	<?php 
	if(is_null($rows) || count($rows) == 0) {
		?>
		<tr><td>None</td></tr>
		<?php
	} else {
		?>
		<tr>
			<th>Name/Number</th>
			<th>Description</th>
			<th>Room type</th>
			<th>Price plan</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rows as $row) {
			$rt = $db_func->getRoomType($row->id_room_type);
			$pp = $db_func->getPricePlan($row->id_price_plan);
		?>
		<tr>
			<td><?php echo $row->room_name?></td>
			<td><?php echo $row->room_description; ?></td>
			<td><?php 
				if(!is_null($rt)) {
					echo "{$rt->type_name}";
				} else {
					echo "&nbsp;";
				}
				?>				
			</td>
			<td><?php 
				if(!is_null($pp)) {
					if($pp->price_type == 0) { 
						echo "Per room"; 
					} else if ($pp->price_type == 1) { 
						echo "Per single sharing"; 
					}
					echo " | " . $pp->base_amount . " | " . factorToPercentage($pp->single_factor) . "%";
				} else {
					echo "&nbsp;";
				}
				?>				
			</td>
			<td><button><?php echo $row->id; ?> +</button><button><?php echo $row->id; ?> -</button></td>
		</tr>
		<?php
		}
	}
	?>
	</table>