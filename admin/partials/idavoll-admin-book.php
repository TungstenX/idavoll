<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rows = $db_func->getAllRooms();
		$room_type_rows = $db_func->getAllRoomTypes();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_room = -1;
		$selected_capacity_type = "";
		$selected_max = "";
		$selected_price_factor = 1.0;
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Book a room', $this->plugin_name); ?></h2>	
	<p>Book a room for a customer</p>
	<form method="post" name="book" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="book_room_in" />
		<table>
        	<tr>    			
        		<!-- From -->
        		<td><span><?php esc_attr_e('From: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('From: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-from">			                
			                <input type="date" value='<?php if(!empty($selected_from)) {echo $selected_from;} ?>' id="<?php echo $this->plugin_name; ?>-book-from" name="<?php echo $this->plugin_name; ?>-book[from]" required />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- To -->
        		<td><span><?php esc_attr_e('To: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('To: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-to">			                
			                <input type="date" value='<?php if(!empty($selected_to)) {echo $selected_to;} ?>' id="<?php echo $this->plugin_name; ?>-book-to" name="<?php echo $this->plugin_name; ?>-book[to]" required />
			            </label>
			        </fieldset>
			    </td>
			</tr>

			<?php
			$capacity_types = $db_func->getAllCapacityItems();
			if(is_null($capacity_types) || count($capacity_types) == 0) {
			?>
				<tr><td colspan="2" style="color: red;">ERROR: None</td></tr>
			<?php
			} else {
				foreach ($capacity_types as $key => $capacity_type) {
			?>
			<tr>
				<td><?php echo $capacity_type->capacity_type; if ($capacity_type->main_capacity) {echo "(Main)"; } ?></td>
				<td>
					<input type="number" step="1" min="<?php if ($capacity_type->main_capacity) {echo '1'; } else { echo '0';} ?>" id="<?php echo $this->plugin_name; ?>-book-cap_item_<?php echo $key; ?>" name="<?php echo $this->plugin_name; ?>-book[cap-item][<?php echo $key; ?>]"  <?php if ($capacity_type->main_capacity) {echo 'required'; } ?> />
					<input type="hidden" id="<?php echo $this->plugin_name; ?>-book-cap_item_id_<?php echo $key; ?>" name="<?php echo $this->plugin_name; ?>-book[cap-item-id][<?php echo $key; ?>]" value="<?php echo $capacity_type->id; ?>" />
				</td>
			</tr>
			<?php
				}
			}
			?>
			<tr style="display: none;">
				<td colspan="2">
					<button disabled="disabled" id="<?php echo $this->plugin_name; ?>-book-check"><?php _e('Check availability', $this->plugin_name); ?></button>
					<script type="text/javascript" >
			            	jQuery(document).on("click", ".<?php echo $this->plugin_name; ?>-book-check", function(e) {
			            		e.preventDefault();
			            		var cap_items = $( "input[name=<?php echo $this->plugin_end; ?>-book-cap-item]" ).val();
			            		var cap_items_id = $( "input[name=<?php echo $this->plugin_end; ?>-book-cap-item-id]" ).val();			
			            		console.log("cap_items");
			            		console.log(cap_items);
			            		console.log(cap_items_id);
								var data = {
									'action': 'rooms_available',
									'start_date': $( "<?php echo $this->plugin_name; ?>-book-from" ).val(),
									'end_date': $( "<?php echo $this->plugin_end; ?>-book-to" ).val(),
									'cap_items': cap_items,									
									'cap_items_id': cap_items_id
								};
								jQuery.post(ajaxurl, data, function(response) {	
									alert('Got this from the server: ' + response);
									var myObj = JSON.parse(response);
									if(myObj && myObj[0]) {
									}
								});
								return false;
							});
					</script>
				</td>
			</tr>

			<tr>    			
        		<!-- Room type -->
        		<td><span><?php esc_attr_e('Room type: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room type: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-room_type">			                
			                <select id="<?php echo $this->plugin_name; ?>-book-room_type" class="<?php echo $this->plugin_name; ?>-book-room_type" name="<?php echo $this->plugin_name; ?>-book[room_type]" >
			                	<option value="-1">Any</option>
			            		<?php 
			            		if (!is_null($room_type_rows) && (count($room_type_rows) > 0)) {
				            		foreach ($room_type_rows as $key => $value) {			            			
				            			echo "<option value=\"{$value->id}\" ";
				            			if($selected_room_type == $value->id) { 
				            				echo "selected "; 
				            			}
				            			echo ">";
										echo __($value->type_name, $this->plugin_name); 
										echo "</option>";
				            		}
			            		}
			            		?>
			            	</select>
			            	<script type="text/javascript" >
			            	jQuery( ".<?php echo $this->plugin_name; ?>-book-room_type" ).change(function() {
			            		var cap_items = [];
			            		var cap_item_ids = [];
			            		<?php
			            		foreach ($capacity_types as $key => $value) {
			            			echo "\t\tcap_items.push(document.getElementById( \"{$this->plugin_name}-book-cap_item_{$key}\" ).value);\n";
			            			echo "\t\tcap_item_ids.push(document.getElementById( \"{$this->plugin_name}-book-cap_item_id_{$key}\" ).value);\n";
			            		}
			            		?>
			            		var start_date = document.getElementById( "<?php echo $this->plugin_name; ?>-book-from" ).value; 
			            		var end_date = document.getElementById( "<?php echo $this->plugin_name; ?>-book-to" ).value; 
								var data = {
									'action': 'rooms_from_room_type',
									'data': {'room_type_id': this.value,
									'start_date': start_date,
									'end_date': end_date,
									'cap_items': cap_items,
									'cap_item_ids': cap_item_ids}
								};
								jQuery.post(ajaxurl, data, function(response) {	
									// alert('Got this from the server: ' + response);
									var myObj = JSON.parse(response);
									if(myObj) {
										//when ok remove items
										var selectId = "<?php echo $this->plugin_name; ?>-book-room";
										var select = document.getElementById(selectId);
										while(select.hasChildNodes()) {
											select.removeChild(select.childNodes[0]);  
										}
										//Add any entry
										var anyOption = document.createElement("OPTION"); 
										anyOption.setAttribute("value", "-1");
										var anyLabel = document.createTextNode("<?php _e('Any', $this->plugin_end); ?>"); 
										anyOption.appendChild(anyLabel);
										select.appendChild(anyOption);

										//add entries
										for (i = 0; i < myObj.length; i++) {
											var option = document.createElement("OPTION"); 
											option.setAttribute("value", myObj[i].id);
											var label = document.createTextNode(myObj[i].room_name); 
											option.appendChild(label);
											select.appendChild(option);
										}
									} else {
										//error
										alert("Error!");
									}
								});
							});
						</script>
			            </label>			            
			        </fieldset>
			    </td>
			</tr>

    		<tr>    			
        		<!-- Room -->
        		<td><span><?php esc_attr_e('Room: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-room">			                
			                <select id="<?php echo $this->plugin_name; ?>-book-room" class="<?php echo $this->plugin_name; ?>-book-room" name="<?php echo $this->plugin_name; ?>-book[room]" >
			                	<option value="-1">Any</option>
			            	</select>
			            </label>			            
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Person name -->
        		<td><span><?php esc_attr_e('Contact name: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Contact name: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-contact_name">			                
			                <input type="text" value='<?php if(!empty($selected_contact_name)) {echo $selected_contact_name;} ?>' id="<?php echo $this->plugin_name; ?>-book-contact_name" name="<?php echo $this->plugin_name; ?>-book[contact_name]" required />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Tel -->
        		<td><span><?php esc_attr_e('Contact telephone: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Contact telephone: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-contact_telephone">			                
			                <input type="phone" value='<?php if(!empty($selected_contact_telephone)) {echo $selected_contact_telephone;} ?>' id="<?php echo $this->plugin_name; ?>-book-contact_telephone" name="<?php echo $this->plugin_name; ?>-book[contact_telephone]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Email -->
        		<td><span><?php esc_attr_e('Contact email: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Contact email: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-contact_email">			                
			                <input type="email" value='<?php if(!empty($selected_contact_email)) {echo $selected_contact_email;} ?>' id="<?php echo $this->plugin_name; ?>-book-contact_email" name="<?php echo $this->plugin_name; ?>-book[contact_email]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Deposit -->
        		<td><span><?php esc_attr_e('Deposit received: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Deposit received: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-deposit">			                
			                <input type="checkbox" <?php if(!empty($selected_deposit)) {echo "checked";} ?>' id="<?php echo $this->plugin_name; ?>-book-deposit" name="<?php echo $this->plugin_name; ?>-book[deposit]" value="1" />
			            </label>
			        </fieldset>
			    </td>
			</tr>			
			
			<tr>
				<td colspan="2"><?php submit_button(__('Submit booking', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>