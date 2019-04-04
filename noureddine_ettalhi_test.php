<?php






/*
** Question A
** Your goal for this question is to write a program that accepts two lines (x1,x2) and (x3,x4)
** on the x-axis and returns whether they overlap.
** As an example, (1,5) and (2,6) overlaps but not (1,5) and (6,8).
**
**
*/

function check_overlaps( $line1, $line2 ){

    if( !is_array($line1) || !is_array($line2) ){
        throw new \Exception('Error : Argument type error.');
    }

    // None of the first line edges should be inside the second line.
    if( $line1['x1'] >= min( $line2 ) && $line1['x1'] <= max( $line2 ) ){
        return true;
    }
    if( $line1['x2'] >= min( $line2 ) && $line1['x2'] <= max( $line2 ) ){
        return true;
    }


    // None of the second line edges should be inside the first line.
    if( $line2['x1'] >= min( $line1 ) && $line2['x1'] <= max( $line1 ) ){
        return true;
    }
    if( $line2['x2'] >= min( $line1 ) && $line2['x2'] <= max( $line1 ) ){
        return true;
    }

    return false;

}

// ....................... test 1
$line1 = [
    'x1' => 1,
    'x2' => 5,
];

$line2 = [
    'x1' => 2,
    'x2' => 6,
];
echo "overlaps checks test1: " . check_overlaps( $line1, $line2 ) ."\n";

// ....................... test 2
$line1 = [
    'x1' => 1,
    'x2' => 5,
];

$line2 = [
    'x1' => 6,
    'x2' => 8,
];
echo "overlaps checks test2: " . check_overlaps( $line1, $line2 ) ."\n";








/*
**  Question B
**
**  The goal of this question is to write a software library that accepts 2 version string as
**  input and returns whether one is greater than, equal, or less than the other.
**  As an example: “1.2” is greater than “1.1”. Please provide all test cases you could think of.
*/


function check_versions( $version1, $version2 ){

    $version1_arr = explode(".", $version1 );
    $version2_arr = explode(".", $version2 );

    if( !is_array($version1_arr) || !is_array($version2_arr) ){
        throw new \Exception('Error : Argument type error.');
    }

    if( count($version1_arr) == 0 || count($version2_arr) == 0 ){
        throw new \Exception('Error : Argument empty error.');
    }

    foreach( $version1_arr as $sub_version ){
        if( !is_numeric($sub_version) ){
            throw new \Exception('Error : Sub-version should be numeric in argument1.');
        }
        if( $sub_version < 0 ){
            throw new \Exception('Error : Sub-version should be positive in argument1.');
        }
    }
    foreach( $version2_arr as $sub_version ){
        if( !is_numeric($sub_version) ){
            throw new \Exception('Error : Sub-version should be numeric in argument2.');
        }
        if( $sub_version < 0 ){
            throw new \Exception('Error : Sub-version should be positive in argument2.');
        }
    }


    // ............... Starts comparison here.
    for( $idx = 0; $idx < count($version1_arr); $idx++ ){
        if( !isset($version2_arr[$idx]) ){
            if( $version1_arr[$idx] == 0 ){
                continue;
            } else {
                return 1; // Greater
            }
        }
        if( $version1_arr[$idx] > $version2_arr[$idx] ){
            return 1; // Greater.
        }
        if( $version1_arr[$idx] < $version2_arr[$idx] ){
            return -1; // Smaller.
        }

    }

    if( !isset($version2_arr[$idx - 1]) ){
        return 0; // Equals. version2 has been exausted inside the loop with zeros on version1.
    }
    if( !isset($version2_arr[$idx]) ) {
        return 0; // Equals. version2 and version1 have the same length and same values.
    }

    for( $i = $idx; $i < count($version2_arr); $i++ ){
        if( $version2_arr[$i] > 0 ){
            return -1;
        }
    }

    return 0;


}


// ....................... test 1
$version1 = "5.2.3";
$version2 = "5.2.3";
echo "version1 : $version1, version2 : $version2. Expect : 0\n";
echo "versions check test1: " . check_versions( $version1, $version2 ) ."\n\n";


// ....................... test 2
$version1 = "5.6.3";
$version2 = "5.4.2.9";
echo "version1 : $version1, version2 : $version2. Expect : 1\n";
echo "versions check test1: " . check_versions( $version1, $version2 ) ."\n\n";


// ....................... test 3
$version1 = "5.3.3";
$version2 = "5.4.2.9";
echo "version1 : $version1, version2 : $version2. Expect : -1\n";
echo "versions check test1: " . check_versions( $version1, $version2 ) ."\n\n";

// ....................... test 4
$version1 = "5.3.3.0.0.0.0.0";
$version2 = "5.3.3";
echo "version1 : $version1, version2 : $version2. Expect : 0\n";
echo "versions check test1: " . check_versions( $version1, $version2 ) ."\n\n";

// ....................... test 5
$version1 = "5.3.3";
$version2 = "5.3.3.0.0.0.0.0";
echo "version1 : $version1, version2 : $version2. Expect : 0\n";
echo "versions check test1: " . check_versions( $version1, $version2 ) ."\n\n";


