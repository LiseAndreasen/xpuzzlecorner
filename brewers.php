<?php

///////////////////////////////////////////////////////////////////////////
// constants

// how many times through the loop?
$loops = 100000;

// which question?
// 1-3, 4-6, 7-9
$q = 7;

// number of games in all
// number of games played
if(1 <= $q && $q <= 3) {
	$no_of_games = 162;
	$games_played = 2;
	$brewers_won = $games_played / 2;
} else {
	if(4 <= $q && $q <= 6) {
		$no_of_games = 162;
		$games_played = 30;
		$brewers_won = $games_played / 2;
	} else {
		// values will be defined locally
	}
}

///////////////////////////////////////////////////////////////////////////
// functions

// auxiliary function
// returns random number with flat distribution from 0 to 1
function random_0_1() 
{
    return (float)rand() / (float)getrandmax();
}

function print_histogram($data) {
	$no_of_buckets = sizeof($data);
	$max_bucket = max($data) + 1;
	// print histogram
	// height: 9
	for($i=1;$i<10;$i++) {
		$histogram[$i] = "";
	}
	$histogram[0] = "+";
	// assuming no of buckets less than width of screen
	for($i=1;$i<=1+$no_of_buckets/10;$i++) {
		$histogram[0] .= "---------+";
	}

	for($i=0;$i<$no_of_buckets;$i++) {
		$column_top = (int) (10 * $data[$i] / $max_bucket);
		for($j=1;$j<=$column_top;$j++) {
			$histogram[$j] .= "*";
		}
		for($j=$column_top+1;$j<10;$j++) {
			$histogram[$j] .= " ";
		}
	}
	for($i=9;$i>=0;$i--) {
		print($histogram[$i] . "\n");
	}
}

function print_csv($data) {
	foreach($data as $key => $val) {
		printf("%d;%d\n", $key, $val);
	}
}

function loop_the_tournament($loops, $brewers_won, $games_played,
	$no_of_games, $silent) {
	// tally of games won in many loops
	for($i=0;$i<=$no_of_games;$i++) {
		$brewers_won_total[$i] = 0;
	}

	for($j=0;$j<$loops;$j++) {
		// one time through the tournament
		$brewers_won_local = $brewers_won;
		for($i=$games_played+1;$i<=$no_of_games;$i++) {
			// the probability Brewers will win
			$p = $brewers_won_local / ($i - 1);
			if(random_0_1() < $p) {
				// Brewers won this time
				$brewers_won_local++;
			}
		}

		// how many games did Brewers win this time?
		$brewers_won_total[$brewers_won_local]++;
		
		if($silent != 1 && $j % 100000 == 0) {
			print(".");
		}
	}
	if($silent != 1) {
		print("\n");
	}
	
	//print_histogram($brewers_won_total);

	if($silent != 1) {
		printf("In %d loops, out of %d games, and even after %d,\n" .
			"Brewers won the rest %d times (%.5f),\n" .
			"lost the rest %d times (%.5f)\n" .
			"and played even %d times (%.5f).\n",
			$loops, $no_of_games, $games_played, 
			$brewers_won_total[$no_of_games - $games_played / 2], 
			$brewers_won_total[$no_of_games - $games_played / 2] / $loops,
			$brewers_won_total[$games_played / 2],
			$brewers_won_total[$games_played / 2] / $loops,
			$brewers_won_total[$no_of_games / 2],
			$brewers_won_total[$no_of_games / 2] / $loops);
	}
	
	//print_csv($brewers_won_total);
	$return_val = array(
		$brewers_won_total[$games_played / 2] / $loops,
		$brewers_won_total[$no_of_games / 2] / $loops,
		$brewers_won_total[$no_of_games - $games_played / 2] / $loops
	);
	return $return_val;
}

///////////////////////////////////////////////////////////////////////////
// main program

if($q < 7) {
	loop_the_tournament($loops, $brewers_won, $games_played,
		$no_of_games, 0);
} else {
	$win_string = "      ";
	$even_string = "      ";
	$lose_string = "      ";
	for($m=10;$m<=100;$m+=10) {
		$win_string .= "  $m    ";
		$even_string .= "  $m    ";
		$lose_string .= "  $m    ";
	}
	$win_string .= "\n";
	$even_string .= "\n";
	$lose_string .= "\n";
	for($N=1000;$N<=10000;$N+=1000) {
		$win_string .= sprintf("%5d ", $N);
		$even_string .= sprintf("%5d ", $N);
		$lose_string .= sprintf("%5d ", $N);
		for($m=10;$m<=100;$m+=10) {
			$no_of_games = $N;
			$games_played = $N - $m;
			$brewers_won = $games_played / 2;
			$frac = loop_the_tournament($loops, $brewers_won,
				$games_played, $no_of_games, 1);
			$win_string .= sprintf("%.5f ", $frac[2]);
			$even_string .= sprintf("%.5f ", $frac[1]);
			$lose_string .= sprintf("%.5f ", $frac[0]);
//			printf("%.5f ", $frac[1]);
			print(".");
		}
		$win_string .= "\n";
		$even_string .= "\n";
		$lose_string .= "\n";
		print("\n");
	}
	
	print("\nWinning.\n");
	print($win_string);
	print("\nPlaying even.\n");
	print($even_string);
	print("\nLosing.\n");
	print($lose_string);
}

?>
