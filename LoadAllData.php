<?php
    include("db_connect.php");

    $return_array = array();

    //write a section here to handle processing offline intel gathering!!!

    /*
        IT IS VERY IMPORTNAT TO HAVE AN OFFLINE PROCESSOR BUILT HERE THAT HANDLES EVERYTHING FROM FIRST LOGIN TO ANY LENGTH OF GONE TIME.
    */
	$failed = 0;
    //load player sheet data
    $player_query = mysql_query("SELECT * FROM player_sheet WHERE id='$id'") or die(mysql_error());
    $player_data = mysql_fetch_assoc($player_query);
	if (mysql_num_rows($player_query)<1){
		$failed = 1;
	}

    //load planted bug status data
    $bug_query = mysql_query("SELECT * FROM bugged_locations WHERE owner_id='$id'") or die(mysql_error());
    $bug_data = mysql_fetch_assoc($bug_query);
	if (mysql_num_rows($bug_query)<1){
		$bug_data=0;
	}

	if ($failed==0){
    	//assemble the return array
    	array_push($return_array, "Success");
    	array_push($return_array, $player_data);
    	array_push($return_array, $bug_data);
    	echo json_encode($return_array, JSON_NUMERIC_CHECK);
	} else {
		array_push($return_array, "Failed");
		array_push($return_array, "Unable to locate player record- failed to create in db_connect");
	}

//LoadAllData.php
?>
