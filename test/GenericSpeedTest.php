<?php
/**
 * Created by PhpStorm.
 * User: Ahmed Salah
 * Date: 4/30/2018
 * Time: 10:43 PM
 */


error_reporting( 0 );

$repetitions = 10000;

function getmicrotime() {
    $t = gettimeofday();
    return $t['sec'] * 1000 + $t['usec'] / 1000;
}

function bench() {
    static $start;
    if ( !$start ) {
        $start = getmicrotime();
        return 0;
    }
    $duration = getmicrotime() - $start;
    $start = 0;
    return $duration;
}

function ms( $ms ) {
    echo '<td>';
    if ( $ms <= 0 ) {
        echo 0;
    } elseif ( $ms < 0.5 ) {
        echo '&gt;0';
    } else {
        echo (int)round( $ms );
    }
    echo '&nbsp;ms</td>';
}

function display_bench_results() {
    $html = ob_get_clean();
    preg_match_all( '/>\{([^}]+)\}/is', $html, $matches );
    $min = 0;
    $sum = 0;

    foreach ( $matches[1] as $i => $s ) {
        if ( $min <= 0 ) {
            $min = floatval( $s );
        } else {
            $min = min( $min, floatval( $s ) );
        }
        $sum += floatval( $s );
    }

    foreach ( $matches[1] as $i => $s ) {
        $index = (int) round( floatval( $s ) * 100 / $min );
        if ( $index > 5000 ) $class = 'no';
        elseif ( $index > 500 ) $class = 'buggy';
        elseif ( $index > 200 ) $class = 'incomplete';
        elseif ( $index > 100 ) $class = 'almost';
        else $class = 'yes';
        $html = str_replace( $matches[0][$i], ' class="' . $class . '">' . $index, $html );
    }

    echo $html;
    // echo 'Total: ' . round( $sum ) . ' ms';
}

//------------------------------------------------------------------------------

function empty_array_bench( $method ) {
	$r = (int)round( $GLOBALS['repetitions'] / 5 );

	$array = array();
	bench();
	$caption = $method( $array, $r );
	$d = bench();
	$sum = $d;
	echo '<tr><td><code>' . $caption . '</code></td>';
	ms( $d );

	$array = range( 0, 100 );
	bench();
	$method( $array, $r );
	$d = bench();
	$sum += $d;
	ms( $d );

	ms( $sum );
	echo '<td>{' . $sum . '}</td></tr>';
}

function empty_array_method1( &$array, $r ) {
	while ( $r-- ) {
		$empty = count( $array ) === 0;
	}
	return 'count($array) === 0 //by reference';
}

function empty_array_method2( $array, $r ) {
	while ( $r-- ) {
		$empty = count( $array ) === 0;
	}
	return 'count($array) === 0 //by value';
}

function empty_array_method3( &$array, $r ) {
	while ( $r-- ) {
		$empty = $array === array();
	}
	return '$array === []';
}

function empty_array_method4( &$array, $r ) {
	while ( $r-- ) {
		$empty = empty( $array );
	}
	return 'empty($array)';
}

function empty_array_method5( &$array, $r ) {
	while ( $r-- ) {
		$notEmpty = (bool)$array;
	}
	return '(bool)$array';
}

//------------------------------------------------------------------------------

function strcmp_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] );

    bench();
    $caption = $method( 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz', $r );
    $d = bench();
    $sum = $d;
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $d );

    bench();
    $method( '0bcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz', $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method( 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxy0', $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function strcmp_method1( $var, $r ) {
	$equal = false;
	$b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
	while ( $r-- ) {
		$equal = $var == $b;
	}
	return '$a == $b';
}

function strcmp_method2( $var, $r ) {
    $equal = false;
    $b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
    while ( $r-- ) {
	$equal = $var === $b;
    }
    return '$a === $b';
}

function strcmp_method3( $var, $r ) {
    $equal = false;
    $b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
    while ( $r-- ) {
	$equal = !strcmp( $var, $b );
    }
    return '!strcmp($a, $b)';
}

function strcmp_method4( $var, $r ) {
    $equal = false;
    $b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
    while ( $r-- ) {
	$equal = strcmp( $var, $b ) == 0;
    }
    return 'strcmp($a, $b) == 0';
}

function strcmp_method5( $var, $r ) {
    $equal = false;
    $b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
    while ( $r-- ) {
	$equal = strcmp( $var, $b ) === 0;
    }
    return 'strcmp($a, $b) === 0';
}

function strcmp_method6( $var, $r ) {
    $equal = false;
    $b = 'abcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyzabcdefghijklmnopqrstuvwxyz';
    while ( $r-- ) {
	$equal = strcasecmp( $var, $b ) === 0;
    }
    return 'strcasecmp($a, $b) === 0';
}

//------------------------------------------------------------------------------

function array_get_bench( $method ) {
    $array = array();
    for ( $i = 0; $i < 100; $i++ ) {
        $array[$i] = 'i' . $i;
        $array['key' . $i] = 's' . $i;
    }
    $r = (int)round( $GLOBALS['repetitions'] / 5 );

    bench();
    $caption = $method( $array, $r );
    $d = bench();
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $d );
    echo '<td>{' . $d . '}</td></tr>';
}

function array_get_method1( $array, $r ) {
    while ( $r-- ) {
        for ($i = 0; $i < 100; $i++) $result = $array[$i];
    }
    return '$array[0]';
}

function array_get_method2( $array, $r ) {
    while ( $r-- ) {
        for ($i = 0; $i < 100; $i++) $result = $array[$i];
    }
    return '$array[\'key\']';
}

//------------------------------------------------------------------------------

function empty_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] / 5 );

    bench();
    $caption = $method( -1, $r);
    $d = bench();
    $sum = $d;
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $d );

    bench();
    $method( null, $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method( false, $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method( '', $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method( '0', $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method( '1', $r );
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method(
        'x2345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
        $r
    );
    $d = bench();
    $sum += $d;
    ms( $d );

    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function empty_method1( $var, $r ) {
    if ( $var < 0 ) {
        unset( $var );
    }
    $isEmpty = false;
    while ( $r-- ) {
        if ( !$var ) {
            $isEmpty = true;
        }
    }
    // if ( empty( $var ) !== $isEmpty ) var_dump( $var, 'if (!$var)' );
    return 'if (!$var)';
}

function empty_method2( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ( empty( $var ) ) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (empty($var))');
    return 'if (empty($var))';
}

function empty_method3( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ($var == '') { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if ($var == \'\')');
    return 'if ($var == \'\')';
}

function empty_method4( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ('' == $var) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (\'\' == $var)');
    return 'if (\'\' == $var)';
}

function empty_method5( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ($var === '') { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if ($var === \'\')');
    return 'if ($var === \'\')';
}

function empty_method6( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ('' === $var) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (\'\' === $var)');
    return 'if (\'\' === $var)';
}

function empty_method7( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if (strcmp( $var, '' ) == 0) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (strcmp($var, \'\') == 0)');
    return 'if (strcmp($var, \'\') == 0)';
}

function empty_method8( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if (strcmp( '', $var ) == 0) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (strcmp(\'\', $var) == 0)');
    return 'if (strcmp(\'\', $var) == 0)';
}

function empty_method9( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if (strlen( $var ) == 0) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (strlen($var) == 0)');
    return 'if (strlen($var) == 0)';
}

function empty_method10( $var, $r ) {
    if ($var < 0) unset( $var );
    $isEmpty = false;
    while ( $r-- ) {
        if ( !strlen( $var ) ) { $isEmpty = true; }
    }
    //if (empty( $var ) != $isEmpty) var_dump($var, 'if (!strlen($var))');
    return 'if (!strlen($var))';
}

//------------------------------------------------------------------------------

function strstr_bench( $method, $needle = 'abcd' ) {
	$r = (int)round( $GLOBALS['repetitions'] / 30 );
	$strLen = (int)round( 2000 / 11 );

	bench();
	$caption = $method(
		str_repeat( 'x1234567890', $strLen ),
		$needle,
		$r
	);
	$d = bench();
	$sum = $d;
	echo '<tr><td><code>' . $caption . '</code></td>';
	ms( $d );

	bench();
	$method(
		'abcd' . str_repeat( 'x1234567890', $strLen ),
		$needle,
		$r
	);
	$d = bench();
	$sum += $d;
	ms( $d );

	bench();
	$method(
		str_repeat( 'x1234567890', $strLen / 2 ) . 'abcd' . str_repeat( 'x1234567890', $strLen / 2 ),
		$needle,
		$r
	);
	$d = bench();
	$sum += $d;
	ms( $d );

	bench();
	$method(
		str_repeat( 'x1234567890', $strLen ) . 'abcd',
		$needle,
		$r
	);
	$d = bench();
	$sum += $d;
	ms( $d );

	ms( $sum );
	echo '<td>{' . $sum . '}</td></tr>';
}

function strstr_method1( $haystack, $needle, $r ) {
    $found = false;
    while ( $r-- ) {
        if ( strstr( $haystack, $needle ) ) { $found = true; }
    }
    return 'strstr($haystack, $needle)';
}

function strstr_method2( $haystack, $needle, $r ) {
    $found = false;
    while ( $r-- ) {
        if (strpos( $haystack, $needle ) !== false) { $found = true; }
    }
    return 'strpos($haystack, $needle) !== false';
}

function strstr_method3( $haystack, $needle, $r ) {
    $found = false;
    while ( $r-- ) {
        if (strstr( $haystack, $needle ) !== false) { $found = true; }
    }
    return 'strstr($haystack, $needle) !== false';
}

function strstr_method4( $haystack, $needle, $r ) {
    $found = false;
    while ( $r-- ) {
        if ( stristr( $haystack, $needle ) ) { $found = true; }
    }
    return 'stristr($haystack, $needle)';
}

function strstr_method5( $haystack, $needle, $r ) {
    $found = false;
    $regexp = '/' . preg_quote( $needle, '/' ) . '/';
    while ( $r-- ) {
        if ( preg_match( $regexp, $haystack ) ) { $found = true; }
    }
    return 'preg_match("/$needle/", $haystack)';
}

function strstr_method6( $haystack, $needle, $r ) {
    $found = false;
    $regexp = '/' . preg_quote( $needle, '/' ) . '/i';
    while ( $r-- ) {
        if ( preg_match( $regexp, $haystack ) ) { $found = true; }
    }
    return 'preg_match("/$needle/i", $haystack)';
}

function strstr_method7( $haystack, $needle, $r ) {
    $found = false;
    $regexp = '/' . preg_quote( $needle, '/' ) . '/S';
    while ( $r-- ) {
        if ( preg_match( $regexp, $haystack ) ) { $found = true; }
    }
    return 'preg_match("/$needle/S", $haystack)';
}

/*
function strstr_method8( $haystack, $needle, $r ) {
    $found = false;
    while ( $r-- ) {
        if ( ereg( $needle, $haystack ) ) { $found = true; }
    }
    return 'ereg($needle, $haystack)';
}
*/

//------------------------------------------------------------------------------

function startsWith_method1( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = $haystack[0] === $needle;
    }
    return '$haystack[0] === \'n\'';
}

function startsWith_method2( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strncmp( $haystack, $needle, strlen( $needle ) ) === 0;
    }
    return 'strncmp($haystack, $needle, strlen($needle)) === 0';
}

function startsWith_method3( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strncmp( $haystack, $needle, 1 ) === 0;
    }
    return 'strncmp($haystack, \'needle\', 6) === 0';
}

function startsWith_method4( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strncasecmp( $haystack, $needle, strlen( $needle ) ) === 0;
    }
    return 'strncasecmp($haystack, $needle, strlen($needle)) === 0';
}

function startsWith_method5( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strpos( $haystack, $needle ) === 0;
    }
    return 'strpos($haystack, $needle) === 0';
}

function startsWith_method6( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = substr( $haystack, 0, strlen( $needle ) ) === $needle;
    }
    return 'substr($haystack, 0, strlen($needle)) === $needle';
}

function startsWith_method7( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strcmp( substr( $haystack, 0, strlen( $needle ) ), $needle ) === 0;
    }
    return 'strcmp(substr($haystack, 0, strlen($needle)), $needle) === 0';
}

function startsWith_method8( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = preg_match( '/^' . preg_quote( $needle, '/' ) . '/', $haystack );
    }
    return 'preg_match(\'/^\' . preg_quote($needle, \'/\') . \'/\', $haystack)';
}

//------------------------------------------------------------------------------

function endsWith_method1( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = $haystack[strlen( $haystack ) - 1] === $needle;
    }
    return '$haystack[strlen($haystack) - 1] === \'n\'';
}

function endsWith_method2( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = substr( $haystack, strlen( $haystack ) - strlen( $needle ) ) === $needle;
    }
    return 'substr($haystack, strlen($haystack) - strlen($needle)) === $needle';
}

function endsWith_method3( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = substr( $haystack, -strlen( $needle ) ) === $needle;
    }
    return 'substr($haystack, -strlen($needle)) === $needle';
}

function endsWith_method4( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = substr( $haystack, -1 ) === $needle;
    }
    return 'substr($haystack, -1) === \'n\'';
}

function endsWith_method5( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = strcmp( substr( $haystack, -strlen( $needle ) ), $needle) === 0;
    }
    return 'strcmp(substr($haystack, -strlen($needle)), $needle) === 0';
}

function endsWith_method6( $haystack, $needle, $r ) {
    while ( $r-- ) {
        $result = preg_match( '/' . preg_quote( $needle, '/' ) . '$/', $haystack );
    }
    return 'preg_match(\'/\' . preg_quote($needle, \'/\') . \'$/\', $haystack)';
}

//------------------------------------------------------------------------------

function strreplace_method1( $subject, $search, $r ) {
    $replace = $search;
    while ( $r-- ) {
        $result = str_replace( $search, $replace, $subject );
    }
    return 'str_replace($search, $replace, $subject)';
}

function strreplace_method2( $subject, $search, $r ) {
    $replace = $search;
    $regexp = '/' . preg_quote( $search, '/' ) . '/';
    while ( $r-- ) {
        $result = preg_replace( $regexp, $replace, $subject );
    }
    return 'preg_replace("/$search/", $replace, $subject)';
}

function strreplace_method3( $subject, $search, $r ) {
    $replace = $search;
    $regexp = '/' . preg_quote( $search, '/' ) . '/S';
    while ( $r-- ) {
        $result = preg_replace( $regexp, $replace, $subject );
    }
    return 'preg_replace("/$search/S", $replace, $subject)';
}

function strreplace_method4( $subject, $search, $r ) {
    $replace = $search;
    while ( $r-- ) {
        $result = ereg_replace( $search, $replace, $subject );
    }
    return 'ereg_replace($search, $replace, $subject)';
}

function strreplace_method5( $subject, $search, $r ) {
    $replace = $search;
    $a = array( $search => $replace );
    while ( $r-- ) {
        $result = strtr( $subject, $a );
    }
    return 'strtr($subject, $array)';
}

//------------------------------------------------------------------------------

function trim_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] / 50 );

    bench();
    $caption = $method(
        "x234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890",
        $r);
    $d = bench();
    $sum = $d;
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $d );

    bench();
    $method(
        ",,,,,,,,,,,,,,,,,,,,x234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890",
        $r);
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method(
        "x234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890,,,,,,,,,,,,,,,,,,,,",
        $r);
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method(
        ",,,,,,,,,,,,,,,,,,,,x234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890\n1234567890,,,,,,,,,,,,,,,,,,,,",
        $r);
    $d = bench();
    $sum += $d;
    ms( $d );

    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function trim_method1( $string, $r ) {
    while ( $r-- ) {
        $string = trim( $string, ',' );
    }
    return 'trim($string, \',\')';
}

function trim_method2( $string, $r ) {
    while ( $r-- ) {
        $string = preg_replace( '/^,*|,*$/', '', $string );
    }
    return 'preg_replace(\'/^,*|,*$/\', \'\', $string)';
}

function trim_method3( $string, $r ) {
    while ( $r-- ) {
        $string = preg_replace( '/^,*|,*$/m', '', $string );
    }
    return 'preg_replace(\'/^,*|,*$/m\', \'\', $string)';
}

function trim_method4( $string, $r ) {
    while ( $r-- ) {
        $string = preg_replace( '/^,+|,+$/', '', $string );
    }
    return 'preg_replace(\'/^,+|,+$/\', \'\', $string)';
}

function trim_method5( $string, $r ) {
    while ( $r-- ) {
        $string = preg_replace( '/^,+|,+$/m', '', $string );
    }
    return 'preg_replace(\'/^,+|,+$/m\', \'\', $string)';
}

function trim_method6( $string, $r ) {
    while ( $r-- ) {
        $string = preg_replace( '/^,+/', '', preg_replace( '/,+$/', '', $string ) );
    }
    return 'preg_replace(\'/^,+/\', \'\', preg_replace(\'/,+$/\', \'\', &hellip;))';
}

//------------------------------------------------------------------------------

function split_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] / 10 );

    bench();
    $caption = $method(
        '',
        $r);
    $d = bench();
    $sum = $d;
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $d );

    bench();
    $method(
        'x234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890',
        $r);
    $d = bench();
    $sum += $d;
    ms( $d );

    bench();
    $method(
        'x2345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890,12345678901234567890',
        $r);
    $d = bench();
    $sum += $d;
    ms( $d );

    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function split_method1( $string, $r ) {
    while ( $r-- ) {
        $array = explode( ',', $string );
    }
    return 'explode(\',\', $string)';
}

function split_method2( $string, $r ) {
    while ( $r-- ) {
        $array = split( ',', $string );
    }
    return 'split(\',\', $string)';
}

function split_method3( $string, $r ) {
    while ( $r-- ) {
        $array = preg_split( '/,/', $string );
    }
    return 'preg_split(\'/,/\', $string)';
}

function split_method4( $string, $r ) {
    while ( $r-- ) {
        preg_match_all( '/[^,]+/', $string, $matches );
        $array = $matches[0];
    }
    return 'preg_match_all(\'/[^,]+/\', $string, $matches)';
}

//------------------------------------------------------------------------------

function loop_bench( $method ) {
    $array = array();
    $i = 128;
    while ( $i-- ) {
        $array[] = 'abcd';
    }
    reset( $array );

    $r = (int)round( $GLOBALS['repetitions'] / 200 );

    bench();
    $caption = $method( $array, $r );
    $sum = bench();
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function loop_method1( &$array, $r ) {
    $found = false;
    while ( $r-- ) {
        for ($i = 0; $i < count( $array ); $i++) {
            $found = true;
        }
    }
    return 'for ($i = 0; $i < count($array); $i++) //by reference';
}

function loop_method2( $array, $r ) {
    $found = false;
    while ( $r-- ) {
        for ($i = 0; $i < count( $array ); $i++) {
            $found = true;
        }
    }
    return 'for ($i = 0; $i < count($array); $i++) //by value';
}

function loop_method3( &$array, $r ) {
    $found = false;
    while ( $r-- ) {
        for ($i = 0, $count = count( $array ); $i < $count; $i++) {
            $found = true;
        }
    }
    return 'for ($i = 0, $count = count($array); $i < $count; $i++)';
}

function loop_method4( &$array, $r ) {
    $found = false;
    while ( $r-- ) {
        for ($i = count( $array ) - 1; $i >= 0; $i--) {
            $found = true;
        }
    }
    return 'for ($i = count($array) - 1; $i >= 0; $i--)';
}

function loop_method5( &$array, $r ) {
    $found = false;
    while ( $r-- ) {
        for ($i = count( $array ) - 1; $i >= 0; --$i) {
            $found = true;
        }
    }
    return 'for ($i = count($array) - 1; $i >= 0; --$i)';
}

function loop_method6( &$array, $r ) {
    $found = false;
    while ( $r-- ) {
        $i = count( $array ); while ( $i-- ) {
            $found = true;
        }
    }
    return '$i = count($array); while ($i--)';
}

//------------------------------------------------------------------------------

function concat_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] );
    bench();
    $caption = $method( $r );
    $sum = bench();
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function concat_method1( $r ) {
    $array = array( 'mediumLengthExampleString', 'mediumLengthExampleString', 'mediumLengthExampleString' );
    while ( $r-- ) {
        $string = implode( ' ', $array );
    }
    return 'implode(\' \', $array)';
}

function concat_method2( $r ) {
    $array = array( 'mediumLengthExampleString', 'mediumLengthExampleString', 'mediumLengthExampleString' );
    while ( $r-- ) {
        $string = "$array[0] $array[1] $array[2]";
    }
    return '"$array[0] $array[1] $array[2]"';
}

function concat_method3( $r ) {
    $array = array( 'mediumLengthExampleString', 'mediumLengthExampleString', 'mediumLengthExampleString' );
    while ( $r-- ) {
        $string = $array[0] . ' ' . $array[1] . ' ' . $array[2];
    }
    return '$array[0] . \' \' . $array[1] . \' \' . $array[2]';
}

function concat_method4( $r ) {
    $array = array( 'mediumLengthExampleString', 'mediumLengthExampleString', 'mediumLengthExampleString' );
    while ( $r-- ) {
        $string = sprintf( '%s %s %s', $array[0], $array[1], $array[2] );
    }
    return 'sprintf(\'%s %s %s\', $array[0], $array[1], $array[2])';
}

function concat_method5( $r ) {
    $array = array( 'mediumLengthExampleString', 'mediumLengthExampleString', 'mediumLengthExampleString' );
    while ( $r-- ) {
        $string = vsprintf( '%s %s %s', $array );
    }
    return 'vsprintf(\'%s %s %s\', $array)';
}

//------------------------------------------------------------------------------

function quotes_bench( $method ) {
    $r = (int)round( $GLOBALS['repetitions'] );
    bench();
    $caption = $method( $r );
    $sum = bench();
    echo '<tr><td><code>' . $caption . '</code></td>';
    ms( $sum );
    echo '<td>{' . $sum . '}</td></tr>';
}

function quotes_method1( $r ) {
    while ( $r-- ) {
        $string = 'x2345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678';
    }
    return '\'contains no dollar signs\'';
}

function quotes_method2( $r ) {
    while ( $r-- ) {
        $string = "x2345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678901234567890123456789012345678";
    }
    return '"contains no dollar signs"';
}

function quotes_method3( $r ) {
    while ( $r-- ) {
        $string = 'x234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 12';
    }
    return '\'$variables $are $not $replaced\'';
}

function quotes_method4( $r ) {
    while ( $r-- ) {
        $string = "x234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 1234567890 \$a 12";
    }
    return '"\\$variables \\$are \\$not \\$replaced"';
}

function quotes_method5( $r ) {
    $a = '$a';
    while ( $r-- ) {
        $string = "x234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 1234567890 $a 12";
    }
    return '"$variables $are $replaced"';
}

function quotes_method6( $r ) {
    $a = '$a';
    while ( $r-- ) {
        $string = 'x234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 1234567890 ' . $a . ' 12';
    }
    return '$variables . \' \' . $are . \' \' . $replaced';
}

function quotes_method7( $r ) {
    $a = '$a';
    while ( $r-- ) {
        $string = "x234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 1234567890 " . $a . " 12";
    }
    return '$variables . " " . $are . " " . $replaced';
}

//------------------------------------------------------------------------------

?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=ISO-8859-1" />
    <title>My Collection of PHP Performance Benchmarks</title>
    <style type="text/css">
        body {
            background: #FFF;
            color: #333;
            font: 12px Verdana, sans-serif;
        }
        a {
            border-bottom: 1px dashed #BBB;
            color: #000;
            font-weight: bold;
            text-decoration: none;
        }
        a:hover {
            border: 0;
            color: #00F;
            text-decoration: underline;
        }
        h1, h2, h3 {
            color: #000;
            font-family: "Lucida Sans", "Lucida Sans Unicode", Verdana, sans-serif;
            margin: 1.5em 0 0.5em;
            padding: 0;
            text-shadow: #CCC 2px 2px 4px;
        }
        h1 {
            margin-top: 0;
        }
        h2 a, h2 a:hover {
            border: 0;
            color: #000;
            text-decoration: none;
        }
        h2 a:hover:after {
            color: #999;
            content: " #";
        }
        table {
            border-collapse: separate;
            border-spacing: 1px;
            empty-cells: show;
        }
        tr:hover {
            background: #EEF;
        }
        th, td {
            padding: 0.2em 1em;
            text-align: left;
            vertical-align: top;
        }
        th {
            background: #777;
            color: #FFF;
            padding: 0.4em 1em;
        }
        td {
            border-bottom: 1px solid #DDD;
        }
        td.right, td + td {
            text-align: right;
        }
        td a {
            border: 0;
        }
        .yes {
            background: #00882D;
            border: 0;
            color: #FFF;
        }
        .almost {
            background: #40A662;
            border: 0;
            color: #FFF;
        }
        .incomplete {
            border: 1px solid #00882D;
            color: #00882D;
        }
        .buggy {
            background: #DA4C57;
            border: 0;
            color: #FFF;
        }
        .no {
            background: #CB000F;
            border: 0;
            color: #FFF;
        }
        address {
            font-style: normal;
            margin: 1.5em 0;
            text-align: right;
        }
    </style>
</head>
<body>

<h1>My PHP Performance Benchmarks</h1>

<p>PHP version <?php echo phpversion(); ?> is running on this server.
    The benchmarks are done live. Reload the page to get fresh numbers.
    You are free to use <a href="php-performance-benchmarks.phps">the source</a> for whatever you want.
    Giving credits to me (<a href="http://maettig.com/">Thiemo M&auml;ttig</a>) would be nice.</p>

<p>Please note that these are micro benchmarks. Micro benchmarks are stupid.
    I created this comparison to learn something about PHP and how the PHP compiler works.
    This can not be used to compare PHP versions or servers.</p>

<h2 id="strlen"><a href="#strlen">Check if a String is empty</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Undefined</th>
        <th>Null</th>
        <th>False</th>
        <th>Empty string</th>
        <th>String '0'</th>
        <th>String '1'</th>
        <th>Long string</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'empty_method' . $i;
        if ( !function_exists( $method ) )
            break;
        empty_bench( $method );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    <s>In most cases,</s> Do not use <code>empty()</code> because it does not trigger a warning when used with undefined variables.
    Note that <code>empty('0')</code> returns true.
    Use <code>strlen()</code> if you want to detect <code>'0'</code>.
    Try to avoid <code>==</code> at all because it may cause strange behaviour
    (e.g. <code>'9a' == 9</code> returns true).
    Prefer <code>===</code> over <code>==</code> and <code>!==</code> over <code>!=</code> if possible
    because it does compare the variable types in addition to the contents.
</p>

<h2 id="empty"><a href="#empty">Check if an Array is empty</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Empty array</th>
        <th>100 elements</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'empty_array_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        empty_array_bench( $method );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion: Why count if you don't care about the exact number?
</p>

<h2 id="strcmp"><a href="#strcmp">Compare two Strings</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Equal</th>
        <th>First character not equal</th>
        <th>Last character not equal</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'strcmp_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        strcmp_bench( $method );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion: Use what fits your needs.</p>

<h2 id="indexof"><a href="#indexof">Check if a String contains another String</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Not found</th>
        <th>Found at the start</th>
        <th>Found in the middle</th>
        <th>Found at the end</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'strstr_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        strstr_bench( $method );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    It does not matter if you use <code>strstr()</code> or <code>strpos()</code>.
    Use the <code>preg&hellip;()</code> functions only if you need the power of regular expressions.
    Never use the <code>ereg&hellip;()</code> functions.</p>

<h2 id="startswith"><a href="#startswith">Check if a String starts with another String</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Not found</th>
        <th>Found at the start</th>
        <th>Found in the middle</th>
        <th>Found at the end</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'startsWith_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        strstr_bench( $method, 'a' );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    <code>strpos()</code> is very fast and can be used in almost all cases.
    <code>strncmp()</code> is good if you are looking for a constant length needle.</p>

<h2 id="endswith"><a href="#endswith">Check if a String ends with another String</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Not found</th>
        <th>Found at the start</th>
        <th>Found in the middle</th>
        <th>Found at the end</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'endsWith_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        strstr_bench( $method, 'a' );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    Using <code>substr()</code> with a negative position is a good trick.</p>

<h2 id="replace"><a href="#replace">Replace a String inside another String</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Not found</th>
        <th>Found at the start</th>
        <th>Found in the middle</th>
        <th>Found at the end</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    strstr_bench( 'strreplace_method1' );
    strstr_bench( 'strreplace_method2' );
    strstr_bench( 'strreplace_method3' );
    strstr_bench( 'strreplace_method4' );
    strstr_bench( 'strreplace_method5' );
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    Never use the <code>ereg&hellip;()</code> functions.</p>

<h2 id="trim"><a href="#trim">Trim Characters from the Beginning and End of a String</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Not found</th>
        <th>Found at start</th>
        <th>Found at end</th>
        <th>Found at both sides</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    trim_bench( 'trim_method1' );
    trim_bench( 'trim_method2' );
    trim_bench( 'trim_method3' );
    trim_bench( 'trim_method4' );
    trim_bench( 'trim_method5' );
    trim_bench( 'trim_method6' );
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    Always benchmark your regular expressions!
    In this case, with <code>.*</code> you also replace nothing with nothing which takes time
    because there is a lot of &ldquo;nothing&rdquo; in every string.</p>

<h2 id="split"><a href="#split">Split a String into an Array</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Empty string</th>
        <th>Single occurrence</th>
        <th>Multiple occurrences</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    split_bench( 'split_method1' );
    split_bench( 'split_method2' );
    split_bench( 'split_method3' );
    split_bench( 'split_method4' );
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    Don't use <code>split()</code>. It's deprecated since PHP 5.3 and removed since PHP 7.</p>

<h2 id="loop"><a href="#loop">Loop a numerical indexed Array of Strings</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    loop_bench( 'loop_method1' );
    loop_bench( 'loop_method2' );
    loop_bench( 'loop_method3' );
    loop_bench( 'loop_method4' );
    loop_bench( 'loop_method5' );
    loop_bench( 'loop_method6' );
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    <code>count()</code> can be horribly slow when PHP's copy-on-write kicks in. Always precalculate it, if possible.</p>

<h2 id="keys"><a href="#keys">Get Elements from an Array</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    for ( $i = 1; ; $i++ ) {
        $method = 'array_get_method' . $i;
        if ( !function_exists( $method ) ) {
            break;
        }
        array_get_bench( $method );
    }
    display_bench_results();
    ?>
</table>
<p>My conclusion: I like associative arrays.</p>

<h2 id="implode"><a href="#implode">Implode an Array</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    concat_bench( 'concat_method1' );
    concat_bench( 'concat_method2' );
    concat_bench( 'concat_method3' );
    concat_bench( 'concat_method4' );
    concat_bench( 'concat_method5' );
    display_bench_results();
    ?>
</table>
<p>My conclusion: String concatenation is a cheap operation in PHP. Don't waste your time benchmarking this.</p>

<h2 id="quotes"><a href="#quotes">The single vs. double Quotes Myth</a></h2>
<table>
    <tr>
        <th>Method</th>
        <th>Summary</th>
        <th>Index</th>
    </tr>
    <?php
    ob_start();
    quotes_bench( 'quotes_method1' );
    quotes_bench( 'quotes_method2' );
    quotes_bench( 'quotes_method3' );
    quotes_bench( 'quotes_method4' );
    quotes_bench( 'quotes_method5' );
    quotes_bench( 'quotes_method6' );
    quotes_bench( 'quotes_method7' );
    display_bench_results();
    ?>
</table>
<p>My conclusion:
    It does not matter if you use single or double quotes at all.
    The inclusion of variables has a measurable effect, but that's independent from the quotes.</p>

<address>&copy; <a href="http://maettig.com/">Thiemo M&auml;ttig</a>,
    created in September 2008, updated in August 2017<br />
    <a href="./">More PHP experiments &raquo;</a></address>

</body>
</html>