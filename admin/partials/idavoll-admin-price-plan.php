<?php
/**
* TODO: 
* 	- All edits after saved button functionality
*   - All remove after saved button functionality
*	- Remove price plan item from price plan after it is saved
*/
?>

	<!-- Price plan items -->
	<?php 
    	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
    	$db_func = new Idavoll_DB_Func();
		$rowsPPItems = $db_func->getAllPricePlanItems();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_item_name = "";
		$selected_day_of_week = -1;
		$selected_start_date = "";
		$selected_start_end = "";		
		$selected_factor = 100.00;
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Price Plan Items', $this->plugin_name); ?></h2>	
	<p>Price plans are created for peak season times.</p>
	<h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?>
	<form method="post" name="booking_price_plan_item" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="price_plan_item_in" />
		<table>
        	<tr>    			
        		<!-- name -->
        		<td><span><?php esc_attr_e('Name: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Name: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-item-item_name">			                
			                <input type="text" value="<?php if($selected_item_name) {echo $selected_item_name;} ?>" id="<?php echo $this->plugin_name; ?>-price-plan-item-item_name" name="<?php echo $this->plugin_name; ?>-price-plan-item[item_name]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
        	<tr>    			
        		<!-- day_of_week -->
        		<td><span><?php esc_attr_e('Day of the week: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Day of the week: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-item-day_of_week">			                
			            	<select id="<?php echo $this->plugin_name; ?>-price-plan-item-day_of_week" name="<?php echo $this->plugin_name; ?>-price-plan-item[day_of_week]">
			            		<?php 
			            		$day_of_week = array('-1' => "Not used", '1' => "Monday", '2' => "Tuesday", '3' => "Wednesday", 
			            			'4' => "Thursday", '5' => "Friday", '6' => "Saturday", '7' => "Sunday");
			            		foreach ($day_of_week as $key => $value) {			            			
			            			echo "<option value=\"$key\" ";
			            			if($selected_day_of_week == $key) { 
			            				echo "selected "; 
			            			}
			            			echo ">" . __($value, $this->plugin_name) . "</option>";
			            		}
			            		?>
			            	</select>
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<td colspan="2"><?php _e('Or', $this->plugin_name); ?></td>
        	</tr>
    		<tr>    			
        		<!-- start date -->
        		<td><span><?php esc_attr_e('Start date: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Start date: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-item-start_date">			                
			                <input type="date" value="<?php if($selected_start_date) {echo $selected_start_date;} ?>" id="<?php echo $this->plugin_name; ?>-price-plan-item-start_date" name="<?php echo $this->plugin_name; ?>-price-plan-item[start_date]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
    		<tr>    			
        		<!-- start date -->
        		<td><span><?php esc_attr_e('End date: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('End date: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-item-end_date">			                
			                <input type="date" value="<?php if($selected_end_date) {echo $selected_end_date;} ?>" id="<?php echo $this->plugin_name; ?>-price-plan-item-end_date" name="<?php echo $this->plugin_name; ?>-price-plan-item[end_date]" />
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>    			
        		<!-- Factor -->
        		<td><span><?php esc_attr_e('Factor: ', $this->plugin_name); ?></span></td>
        		<td>
			        <fieldset>
			            <legend class="screen-reader-text"><span><?php _e('Factor: ', $this->plugin_name); ?></span></legend>
			            <label for="<?php echo $this->plugin_name; ?>-price-plan-item-factor">			                
			                <input type="number" step="0.01" value='<?php if(!empty($selected_factor)) {echo factorToPercentage($selected_factor);} ?>'  id="<?php echo $this->plugin_name; ?>-price-plan-item-factor" name="<?php echo $this->plugin_name; ?>-price-plan-item[factor]" /> %
			            </label>
			        </fieldset>
			    </td>
			</tr>
			<tr>
				<td colspan="2"><?php submit_button(__('Save price plan item', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>

	<!-- List of: -->
	<h2 class="nav-tab-wrapper"><?php _e('Price plan items', $this->plugin_name); ?></h2>
	<table border="1">
	<?php 

	if(is_null($rowsPPItems) || count($rowsPPItems) == 0) {
		?>
		<tr><td>None</td></tr>
		<?php
	} else {
		?>
		<tr>
			<th>Name</th>
			<th>Day of the week</th>
			<th>Start date</th>
			<th>End date</th>
			<th>Single factor</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rowsPPItems as $rowPPI) {
		?>
		<tr>
			<td><?php echo $rowPPI->item_name; ?></td>
			<td><?php echo __($day_of_week["{$rowPPI->day_of_week}"], $this->plugin_name); ?></td>
			<td><?php echo $rowPPI->start_date; ?></td>
			<td><?php echo $rowPPI->end_date; ?></td>
			<td align="right"><?php echo factorToPercentage($rowPPI->factor) . " %"; ?></td>
			<td><button><?php echo $rowPPI->id; ?></button></td>
		</tr>
		<?php
		}
	}
	?>
	</table>
	<br />
	<br />
	<!-- Price plan -->
	<?php 
		$rows = $db_func->getAllPricePlans();
		// echo "<pre>" . print_r($rows, 1) . "</pre>";
		$selected_base_amount = 0.0;
		$selected_single_factor = 100.00;
		$selected_price_type = 1;
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Price Plans', $this->plugin_name); ?></h2>
	<p>Price plans will be linked to room types</p>
	<h3 class="nav-tab-wrapper"><?php _e('New / Edit', $this->plugin_name); ?></h3>
	<form method="post" name="booking_price_plan" action="<?php echo admin_url( 'admin.php' ); ?>">
		<input type="hidden" name="action" value="price_plan_in" />
		<table>
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
			                <input type="number" step="1.00" value="<?php if($selected_base_amount) {echo $selected_base_amount;} ?>" id="<?php echo $this->plugin_name; ?>-price-plan-base_amount" name="<?php echo $this->plugin_name; ?>-price-plan[base_amount]" />
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
			                <input type="number" step="0.01" value='<?php if(!empty($selected_single_factor)) {echo factorToPercentage($selected_single_factor);} ?>'  id="<?php echo $this->plugin_name; ?>-price-plan-single_factor" name="<?php echo $this->plugin_name; ?>-price-plan[single_factor]" /> %
			            </label>
			        </fieldset>
			    </td>
			</tr>

			<tr>    			
        		<!-- Price plan item -->
        		<td><span><?php esc_attr_e('Price plan item: ', $this->plugin_name); ?></span></td>
        		<td>
        			<select id="tmp_price_plan_item" name="tmp_price_plan_item">
        				<?php 
        				if (!is_null($rowsPPItems) && (count($rowsPPItems) > 0)) {
	        				foreach ($rowsPPItems as $key => $value) {
	        					$per = factorToPercentage($value->factor);
	        					$dow = $value->day_of_week == -1 ? "Date" : "Day of the Week";
	        					echo "<option value=\"{$value->id}\">{$value->item_name} | $dow | $per %</option>";
	        				}
	        			}
        				?>
        			</select>
        			<script type="text/javascript">
        				var tmp_price_plan_item_counter = 0;
						// tmp_price_plan_item_button
						//Defining a listener for our button, specifically, an onclick handler
						function add_tmp_price_plan_item() {
						    //First things first, we need our text:
							var e = document.getElementById("tmp_price_plan_item");
							var id = e.options[e.selectedIndex].value;
							var text = e.options[e.selectedIndex].text;
							var hidden = "<input type=\"hidden\" value=\"" + id + "\" name=\"idavoll-price-plan[price_item][" + tmp_price_plan_item_counter + "]\"" + "id=\"idavoll-price-plan-price_item-" + tmp_price_plan_item_counter + "\" />"
						    //Now construct a quick list element
							var elLI = document.createElement('li');
							elLI.innerHTML = text + " <button onclick=\"remove_tmp_price_plan_item('" + tmp_price_plan_item_counter + "')\">-</button>" + hidden;
							elLI.setAttribute("id", "idavoll-price-plan-price_item-li-" + tmp_price_plan_item_counter);

						    //Now use appendChild and add it to the list!
						    document.getElementById("tmp_price_plan_item_list").appendChild(elLI);
						    tmp_price_plan_item_counter++;
						}

						function remove_tmp_price_plan_item(counter) {
							var item = document.getElementById("idavoll-price-plan-price_item-li-" + counter);
							item.parentNode.removeChild(item);
						}
        			</script>
        			<button id="tmp_price_plan_item_button" onclick="add_tmp_price_plan_item();return false;">+</button><br />
        			<ul id="tmp_price_plan_item_list">
        			</ul>
			    </td>
			</tr>
			<tr>
				<td colspan="2"><?php submit_button(__('Save price plan', $this->plugin_name), 'primary','submit', TRUE); ?></td>
			</tr>
		</table>
	</form>

	<!-- List of: -->
	<h2 class="nav-tab-wrapper"><?php _e('Price plans', $this->plugin_name); ?></h2>
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
			<th>Price plan items</th>
			<th>Action</th>
		</tr>
		<?php
		foreach ($rows as $row) {
			$ppis = $db_func->getPricePlanItems($row->id);
		?>
		<tr>
			<td><?php if($row->price_type == 0) { echo "Per room"; } else if ($row->price_type == 1) { echo "Per single sharing"; } ?></td>
			<td><?php echo $row->base_amount; ?></td>
			<td><?php echo $row->single_factor; ?></td>
			<td><?php 
				if(!is_null($ppis) && (count($ppis) > 0)) {
					foreach ($ppis as $key => $value) {
						$per = factorToPercentage($value->factor);
    					$dow = $value->day_of_week == -1 ? "Date" : "Day of the Week";
    					echo "{$value->item_name} | $dow | $per %<br />";
					}
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