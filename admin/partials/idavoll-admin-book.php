<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rows = $db_func->getAllRooms();
		$caprows = $db_func->getAllRooms();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_room = -1;
		$selected_capacity_type = "";
		$selected_max = "";
		$selected_price_factor = 1.0;
	?>
	<p>Description blah</p>
	<h3 class="nav-tab-wrapper"><?php _e('Book a room', $this->plugin_name); ?></h3>
	<form method="post" name="book" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="book_room_in" />
		<table>
    		<tr>    			
        		<td colspan="2"><h3 class="nav-tab-wrapper"><?php _e('Book a room', $this->plugin_name); ?></h3></td>
        	</tr>
        	<tr>    			
        		<!-- Start date -->
        		<td><span><?php esc_attr_e('Start date: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Start date: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-start_date">			                
			                <input type="date" value="<?php if($selected_start_date) {echo $selected_start_date;} ?>" id="<?php echo $this->plugin_name; ?>-room-start_date" name="<?php echo $this->plugin_name; ?>-room[start_date]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- End date -->
        		<td><span><?php esc_attr_e('End date: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('End date: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-room-end_date">			                
			                <input type="date" value="<?php if($selected_end_date) {echo $selected_end_date;} ?>" id="<?php echo $this->plugin_name; ?>-room-end_date" name="<?php echo $this->plugin_name; ?>-room[end_date]" />
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
					<input type="number" step="1" min="<?php if ($capacity_type->main_capacity) {echo '1'; } else { echo '0';} ?>" id="<?php echo $this->plugin_name; ?>-room-cap_item_<?php echo $key; ?>" name="<?php echo $this->plugin_name; ?>-room-cap-item[<?php echo $key; ?>]" />
					<input type="hidden" id="<?php echo $this->plugin_name; ?>-room-cap_item_id_<?php echo $key; ?>" name="<?php echo $this->plugin_name; ?>-room-cap-item-id[<?php echo $key; ?>]" value="<?php echo $capacity_type->id; ?>" />
				</td>
			</tr>
			<?php
				}
			}
			?>
			<tr>
				<td colspan="2">
					<button id="<?php echo $this->plugin_name; ?>-book-check"><?php _e('Check availability', $this->plugin_name); ?></button>
					<script type="text/javascript" >
			            	jQuery(document).on("click", ".<?php echo $this->plugin_name; ?>-book-room", function(e) {
			            		e.preventDefault();
			            		var cap_items = $( "input[name=<?php echo $this->plugin_end; ?>-room-cap-item]" ).val();
			            		var cap_items_id = $( "input[name=<?php echo $this->plugin_end; ?>-room-cap-item-id]" ).val();			
			            		console.log("cap_items");
			            		console.log(cap_items);
			            		console.log(cap_items_id);
								var data = {
									'action': 'rooms_available',
									'start_date': $( "<?php echo $this->plugin_name; ?>-room-start_date" ).val(),
									'end_date': $( "<?php echo $this->plugin_end; ?>-room-start_date" ).val(),
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
        		<!-- Room -->
        		<td><span><?php esc_attr_e('Room: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Room: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-room">			                
			                <select id="<?php echo $this->plugin_name; ?>-book-room" class="<?php echo $this->plugin_name; ?>-book-room" name="<?php echo $this->plugin_name; ?>-book[room]" >
			                	<option value="-1">Please select</option>
			            		<?php 
			            		if (!is_null($rows) && (count($rows) > 0)) {
				            		foreach ($rows as $key => $value) {			            			
				            			echo "<option value=\"{$value->id}|{$value->id_room_type}\" ";
				            			if($selected_room == $value->id) { 
				            				echo "selected "; 
				            			}
				            			echo ">";
										echo __($value->room_name, $this->plugin_name); 
										echo "</option>";
				            		}
			            		}
			            		?>
			            	</select>
			            </label>
			            <script type="text/javascript" >
			            	jQuery( ".<?php echo $this->plugin_name; ?>-book-room" ).change(function() {
								var data = {
									'action': 'capacity_for_room',
									'room_id': this.value
								};
								jQuery.post(ajaxurl, data, function(response) {	
									// alert('Got this from the server: ' + response);
									var myObj = JSON.parse(response);
									if(myObj && myObj[0]) {
										//when ok remove items
										var table = document.getElementById("<?php echo $this->plugin_name; ?>-capacity-table");
										while(table.hasChildNodes()) {
											table.removeChild(table.childNodes[0]);  
										}
										//add entries
										var tr = document.createElement("TR"); 
										var tdL = document.createElement("TD"); 
										var tdR = document.createElement("TD"); 
										var label = document.createTextNode(myObj[0].capacity_type); 
										tdL.appendChild(label);
										
										var input = document.createElement("INPUT"); 
										input.setAttribute("name", "<?php echo $this->plugin_name; ?>-book[main_capacity]");
										input.setAttribute("id", "<?php echo $this->plugin_name; ?>-book-main_capacity");
										input.setAttribute("type", "number");
										input.setAttribute("step", "1");
										input.setAttribute("min", "1");
										input.setAttribute("max", myObj[0].max);

										var inputH = document.createElement("INPUT"); 
										inputH.setAttribute("name", "<?php echo $this->plugin_name; ?>-book[main_capacity_id]");
										inputH.setAttribute("id", "<?php echo $this->plugin_name; ?>-book-main_capacity_id");
										inputH.setAttribute("type", "hidden");
										inputH.setAttribute("value", myObj[0].id);

										tdR.appendChild(input);
										tdR.appendChild(inputH);
										tr.appendChild(tdL);
										tr.appendChild(tdR);
										table.appendChild(tr);
										if(myObj[1] && (myObj[1].length > 0)) {
											for (i = 0; i < myObj[1].length; i++) {
    											var trA = document.createElement("TR"); 
												var tdLA = document.createElement("TD"); 
												var tdRA = document.createElement("TD"); 
												var labelA = document.createTextNode(myObj[1][i].capacity_type); 
												tdLA.appendChild(labelA);
												var inputA = document.createElement("INPUT"); 
												inputA.setAttribute("name", "<?php echo $this->plugin_name; ?>-book[add_capacity][" + i + "]");
												inputA.setAttribute("id", "<?php echo $this->plugin_name; ?>-book-add_capacity_" + i);
												inputA.setAttribute("type", "number");
												inputA.setAttribute("step", "1");
												inputA.setAttribute("min", "0");
												inputA.setAttribute("max", myObj[1][i].max);
												tdRA.appendChild(inputA);

												var inputH = document.createElement("INPUT"); 
												inputH.setAttribute("name", "<?php echo $this->plugin_name; ?>-book[add_capacity_id][" + i + "]");
												inputH.setAttribute("id", "<?php echo $this->plugin_name; ?>-book-add_capacity_" + i);
												inputH.setAttribute("type", "hidden");
												inputH.setAttribute("value", myObj[1][i].id);
												trA.appendChild(tdLA);
												trA.appendChild(tdRA);
												table.appendChild(trA);
											}
										}
									} else {
										//error
										alert("Error!");
									}
								});
							});
						</script>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- From -->
        		<td><span><?php esc_attr_e('From: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('From: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-book-from">			                
			                <input type="date" value='<?php if(!empty($selected_from)) {echo $selected_from;} ?>' id="<?php echo $this->plugin_name; ?>-book-from" name="<?php echo $this->plugin_name; ?>-book[from]" />
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
			                <input type="date" value='<?php if(!empty($selected_to)) {echo $selected_to;} ?>' id="<?php echo $this->plugin_name; ?>-book-to" name="<?php echo $this->plugin_name; ?>-book[to]" />
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
			                <input type="text" value='<?php if(!empty($selected_contact_name)) {echo $selected_contact_name;} ?>' id="<?php echo $this->plugin_name; ?>-book-contact_name" name="<?php echo $this->plugin_name; ?>-book[contact_name]" />
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
        		<!-- Capacity -->
        		<td><span><?php esc_attr_e('Capacity: ', $this->plugin_name); ?></span></td>
        		<td>
			        <table id="<?php echo $this->plugin_name; ?>-capacity-table">
			        	
			        </table>
			    </td>
			</tr>
			<tr>
				<td colspan="2"><?php submit_button(__('Submit booking', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>