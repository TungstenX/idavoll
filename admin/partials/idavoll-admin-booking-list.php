<?php
	require_once plugin_dir_path( __FILE__ ) . '../../includes/class-idavoll-db-func.php';
	$db_func = new Idavoll_DB_Func();
	$rows = $db_func->getBookingList();
    function factorToPercentage($factor) {
    	return number_format((float)$factor * 100.00, 2);
    }
    // echo "<pre>" . print_r($rows, 1) . "</pre>";
	/*
	
SELECT 
b.id AS id,
b.id_admin_user AS id_admin_user,
b.start_date AS booking_start_date,
b.end_date AS booking_end_date,
b.base_amount AS base_amount,
b.contact_name AS contact_name,
b.contact_telephone AS contact_telephone,
b.contact_email AS contact_email,

p.id AS item_id,
p.amount AS amount, 
p.start_date AS start_date, 
p.end_date AS end_date,
p.times AS times,
p.room_capacity_type AS room_capacity_type,
p.price_factor AS price_factor,
p.number_of_ppl AS number_of_ppl,
p.room_name AS room_name 
FROM 
wp_ihs_booking b 
INNER JOIN
wp_ihs_price_item p
ON
b.id = p.id_booking
ORDER BY
b.id DESC, p.start_date
	*/
	?>
	<h2 class="nav-tab-wrapper"><?php _e('Bookings', $this->plugin_name); ?></h2>
	<table class="bookings">
		<tr>
			<th>From</th>
			<th>To</th>
			<th>Customer</th>
			<th>Contact</th>
			<th>Booked by</th>
			<th>Action</th>
		</tr>
	<?php 	
	if(is_null($rows) || count($rows) == 0) {
		?>
		<tr><td colspan="6">None</td></tr>
		<?php
	} else {
		$booking_id = 0;
		$total = 0;
		$class = false;
		foreach ($rows as $row) {	
			if($booking_id != $row->id) {
				if($booking_id != 0) {
					echo "<tr><td colspan=\"8\" align=\"right\">Total</td><td class=\"item_right\">R $total</td></tr>";
					echo "</table></td></tr>";
				}
				$total = 0;
				$booking_id = $row->id;
				$class = !$class;
		?>
			<tr class="booking_row_<?php echo $class ? '1' : '0'; ?>">
				<td><?php echo $row->booking_start_date; ?></td>
				<td><?php echo $row->booking_end_date; ?></td>			
				<td><?php echo $row->contact_name; ?></td>
				<td><?php echo $row->contact_telephone; ?> <?php echo $row->contact_email; ?></td>
				<td><?php echo get_userdata($row->id_admin_user)->user_login; ?></td>
				<td><button id="btn_display_items_<?php echo $booking_id; ?>" onclick="expandPrice('<?php echo $booking_id; ?>');">▼</button></td>
			</tr>
			<tr class="booking_row_item_<?php echo $class ? '1' : '0'; ?>">
				<td colspan="6">
					<table class="bookings_items" id="price_items_<?php echo $booking_id; ?>" style="display: none;">
						<tr>
							<th>Room</th>
							<th>From</th>
							<th>To</th>
							<th>People</th>
							<th>Who</th>
							<th>Factor</th>
							<th>Amount</th>
							<th>Times</th>
							<th>Subtotal</th>
						</tr>
		<?php
			}
		?>
						<tr>
							<td><?php echo $row->room_name; ?></td>
							<td><?php echo $row->start_date; ?></td>			
							<td><?php echo $row->end_date; ?></td>
							<td class="item_right"><?php echo $row->number_of_ppl; ?></td>
							<td><?php echo $row->room_capacity_type; ?></td>
							<td class="item_right"><?php echo factorToPercentage($row->price_factor); ?> %</td>
							<td class="item_right">R <?php echo $row->amount; ?></td>
							<td class="item_right"><?php echo $row->times; ?></td>
							<td class="item_right">R <?php 
							$subtotal = ($row->amount * $row->times);
							$total += $subtotal;
							echo $subtotal; ?></td>
						</tr>
		<?php
		}
		echo "<tr><td colspan=\"8\" align=\"right\">Total</td><td class=\"item_right\">R $total</td></tr>";
		echo "</table></td></tr>";
	}
	?>
	</table>
	<script type="text/javascript">
		function expandPrice(which) {
			var x = document.getElementById('price_items_' + which);
			var y = document.getElementById('btn_display_items_' + which);
		    if (x.style.display === "none") {
		        x.style.display = "block";
		        y.innerHTML = "▲";
		    } else {
		        x.style.display = "none";
		        y.innerHTML = "▼";
		    }
		}
	</script>