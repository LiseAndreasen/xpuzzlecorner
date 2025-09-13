<?php

///////////////////////////////////////////////////////////////////////////
// constants

///////////////////////////////////////////////////////////////////////////
// functions

// double up the configuration 
function double_up($rows, $columns) {
	global $cells;
	for($i=1;$i<=$rows;$i++) {
		for($j=1;$j<=2;$j++) {
			$cells[$i][$j+$columns] = $cells[$i][$j];
		}
	}
	for($i=1;$i<=2;$i++) {
		for($j=1;$j<=$columns+2;$j++) {
			$cells[$i+$rows][$j] = $cells[$i][$j];
			$cells[$i+$rows][$j+$columns] = $cells[$i][$j];
		}
	}
}

// count neighbors
function count_neighbors($height, $width) {
	global $cells, $solutions;
	$neighbors = array();
	// only test cells not on the edge
	for($i=2;$i<$height;$i++) {
		for($j=2;$j<$width;$j++) {
			$my_color = $cells[$i][$j];
			$my_neighbors = 0;
			for($k=$i-1;$k<=$i+1;$k++) {
				for($l=$j-1;$l<=$j+1;$l++) {
					if($cells[$k][$l] == $my_color) {
						$my_neighbors++;
					}
				}
			}
			// subtract my cell
			$my_neighbors--;
			$neighbors[$my_color][$my_neighbors] = 1;
		}
	}
	if(sizeof($neighbors) == 2) {
		// both colors have neighbors
		if(sizeof($neighbors[0]) == 1 && sizeof($neighbors[1]) == 1) {
			// both colors have a stable no of neighbors
			foreach($neighbors[0] as $key1 => $val) {
			}
			foreach($neighbors[1] as $key2 => $val) {
			}
			if(!isset($solutions[$key1][$key2])) {
				$solutions[$key1][$key2] = array($height, $width, $cells);
				printf("Solution for %d/%d neighbors.\n", $key1, $key2);
				print_map($height, $width, $cells);
			}
		}
	}
}

function print_map($height, $width, $cells) {
	for($j=1;$j<=$height;$j++) {
		for($i=1;$i<=$width;$i++) {
			echo $cells[$j][$i];
		}
		echo "\n";
	}
}

// https://stackoverflow.com/questions/28094767/convert-int-into-bits-representation-in-php
function convert_int_to_bits ($iValue, $lgt) {
	$bits = array();  // initialize the array
	do {
		$bits[] = ($iValue & 1);
		$iValue >>= 1;    // shift the bit off so that we go to the next one
	} while ($iValue);  // continue as long as there are still some bits.
	// fill up with zeroes
	$bits_sz = sizeof($bits);
	if($bits_sz < $lgt) {
		$more_bits = array_fill($bits_sz, $lgt - $bits_sz, 0);
		$bits = array_merge($bits, $more_bits);
	}
	// we have the bits in reverse order so lets reverse it.
	return array_reverse($bits);
}

// produce and test pattern
function produce_patterns($x, $y) {
	global $cells, $loops;
	for($counter=0;$counter<pow(2, $x * $y);$counter++) {
		$bits_sq = convert_int_to_bits ($counter, $x * $y);
		for($i=1;$i<=$x;$i++) {
			for($j=1;$j<=$y;$j++) {
				$cells[$i][$j] = $bits_sq[($i - 1) * $x + $j - 1];
			}
		}
		double_up($x, $y);
		count_neighbors($x + 2, $y + 2);
		$loops++;
		if($loops % 100000 == 0) {
			print(".");
		}
	}
}

///////////////////////////////////////////////////////////////////////////
// main program

// remember solutions
$solutions = array();

// count loops
$loops = 0;

// systematically test all pattern sizes, 3x3 until 5x7
// 5x7 takes forever
// this also automatically test 2x3, 2x4 and others
for($x=3;$x<=5;$x++) {
	for($y=$x;$y<=7;$y++) {
		print("Testing ($x, $y) patterns.\n");
		produce_patterns($x, $y);
		print("\n");
	}
}
print("\n");

foreach($solutions as $keyred => $solred) {
	foreach($solred as $keyblue => $solblue) {
		printf("Solution for %d/%d neighbors.\n", $keyred, $keyblue);
		print_map($solblue[0], $solblue[1], $solblue[2]);
	}
}
?>
