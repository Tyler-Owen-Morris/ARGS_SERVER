<?php
    include("db_connect.php");

    $return_array = array();

	$username = isset($_POST['username']) ? protect($_POST['username']) : '';

    //write a section here to handle processing offline intel gathering!!!

    /*
        IT IS VERY IMPORTNAT TO HAVE AN OFFLINE PROCESSOR BUILT HERE THAT HANDLES EVERYTHING FROM FIRST LOGIN TO ANY LENGTH OF GONE TIME.
    */
	$failed = 0;
    //load player sheet data
    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
    $player_data = mysql_fetch_assoc($player_query);
	if (mysql_num_rows($player_query)<1 || mysql_num_rows($player_query)!=1){ //more or less than 1 is invalid
		$failed = 1;
	}

	//check to update username
	$set_name = $player_data['display_name'];
	if ($username <> '' && $set_name == "" && $failed==0) { //if the name is not set on the server, and the player HAS set it in POST data, AND we've successfully found this entry on the DB
		$username_update = mysql_query ("UPDATE player_sheet SET username='$username' WHERE id='$id'") or die(mysql_error());

		//we have to repeat the above query in order to rebuild the $player_data object
		$player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
	    $player_data = mysql_fetch_assoc($player_query);
		if (mysql_num_rows($player_query)<1 || mysql_num_rows($player_query)!=1){ //more or less than 1 is invalid
			$failed = 1;
		}
	}

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


	if ($failed==0){
    	//assemble the return array
    	array_push($return_array, "Success");
    	array_push($return_array, $player_data);
    	array_push($return_array, $bug_array);
	} else {
		array_push($return_array, "Failed");
		array_push($return_array, "Unable to locate player record- failed to create in db_connect");
	}

	echo json_encode($return_array, JSON_NUMERIC_CHECK);


//LoadAllData.php
?>
