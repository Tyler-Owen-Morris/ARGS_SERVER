<?php
    include("db_connect.php");

    $return_array = array();
	
	$bldg_id = isset($_POST['bldg_id']) ? protect($_POST['bldg_id']) : '';
	$bldg_name = isset($_POST['bldg_name']) ? protect($_POST['bldg_name']) : '';
	$cost = isset($_POST['cost']) ? protect($_POST['cost']) : ''; //cost is going to be set

	if ($bldg_id <> '' || $bldg_name <> '' ) {
		
		$existing_query = mysql_query("SELECT * FROM bugged_locations WHERE owner_id='$id' AND d3=0 AND building_id='$bldg_id'") or die(mysql_error());
		$player_query1 = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
		
		if (mysql_num_rows($player_query1)==1) {
			//we have found our entries to update
			$player_data1 = mysql_fetch_assoc($player_query1);
			$intel = $player_data1["intel"];
//			$cost = 1000000000000; //points are in 'bytes'- this is 1PB

			if ($intel >= $cost) {
				$intel = $intel - $cost;
				$bldg_query = "";
				if(mysql_num_rows($existing_query)==0){
					$existing_query1 = mysql_query("SELECT * FROM bugged_locations WHERE owner_id='$id' AND d3=1 AND building_id='$bldg_id'") or die(mysql_error());
					if (mysql_num_rows($existing_query1)==0){
					$bldg_query ="INSERT INTO bugged_locations SET owner_id='$id', bldg_name='$bldg_name', bldg_id='$bldg_id', d3=1";
					}else{
						$bldg_query="UPDATE bugged_locations SET d3=1 WHERE owner_id='$id' AND building_id='$bldg_id'";
					}
				}else{
					$bldg_query="UPDATE bugged_locations SET d3=1 WHERE owner_id='$id' AND building_id='$bldg_id'";
				}
				$bldg_update = mysql_query($bldg_query) or die(mysql_error());
				$bldg_update = mysql_query("UPDATE bugged_locations SET d3=1 WHERE owner_id='$id' AND building_id='$bldg_id'") or die(mysql_error());
				if (mysql_affected_rows()){//if something was affected
					$player_update = mysql_query("UPDATE player_sheet SET intel='$intel' WHERE id='$id' LIMIT 1")or die(mysql_error());
					if(mysql_affected_rows()){
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
						array_push($return_array, "Player not updated- WARNING!!! building WAS updated!!!!");
					}
				}else{
					array_push($return_array, "Failed");
					array_push($return_array, "Building not able to be updated");
				}

			}else{
				array_push($return_array, "Failed");
				array_push($return_array, "Server disagreement with Client- Player has inadequate intel");
			}

		}else {
			array_push($return_array, "Failed");
			array_push($return_array, "Database returned more than one matching entry for player");
		}		
	}else{
		array_push($return_array, "Failed");
		array_push($return_array, "Data not posted correctly to the server");
	}

    echo json_encode($return_array, JSON_NUMERIC_CHECK);	

//DownloadUpgrade1.php
?>
