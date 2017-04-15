<?php
    include("db_connect.php");

    $return_array = array();
	
	$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
	$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
	$cost = isset($_POST['cost']) ? protect($_POST['cost']) : '';

	if ($bldg_id <> '' || $bldg_name <> '' || $cost <> '') {
		
		$existing_query = mysql_query("SELECT * FROM bugged_locations WHERE owner_id='$id' AND building_id='$bldg_id'") or die(mysql_error());
		
		if (mysql_num_rows($existing_query)> 0) {
			$update_query = mysql_query("UPDATE bugged_locations SET t1_bug_count=t1_bug_count+1 WHERE owner_id='$id' AND building_id='$bldg_id'") or die(mysql_error());
		}else{
			$insert_query = mysql_query("INSERT INTO bugged_locations SET owner_id='$id', building_name='$bldg_name', building_id='$bldg_id', t1_bug_count=1, last_download_ts= now()") or die(mysql_error());
		}
		
		//ensure that the building entry was created or updated.
		if (mysql_affected_rows() > 0){
				
				if ($cost > 0){
					$cost_query = mysql_query("UPDATE player_sheet SET intel=intel-$cost WHERE id='$id'") or die(mysql_error());
				}
				if (mysql_affected_rows() >0){
				
					//load new player data
					$player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
					$player_data = mysql_fetch_assoc($player_query);

					//load planted bug status data
					$bug_query = mysql_query("SELECT * FROM bugged_locations WHERE owner_id='$id'") or die(mysql_error());
					$bug_array = array();
					if (mysql_num_rows($bug_query)>0){
						while($bldg = mysql_fetch_assoc($bug_query)){
							array_push($bug_array, $bldg);
						}
					}else{
						$bug_array =null;
					}


					//assemble the return array
					array_push($return_array, "Success");
					array_push($return_array, $player_data);
					array_push($return_array, $bug_array);
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "Failed to save new intel value");
				}
			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "Failed to insert new entry into database");
			}
		
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "Data not posted correctly to the server");
	}

    echo json_encode($return_array, JSON_NUMERIC_CHECK);	

//PlaceT1Bug.php
?>
