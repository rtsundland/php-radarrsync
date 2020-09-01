<?php
# We need 3 variables to complete our connection to the backend server:
#
#	source=address:port[/urlbase]
#
#	source defines the address, port, and URL base of the source Radarr instance
#		- address is the hostname or IP of the remote instance
#		- port and urlbase (if applicable) is the Port Number and URL Base
#		  of the remote instance (Settings > General)
#
#	apikey=<string>
#
#	API key needed to access the source Radarr Instance (Settings > General)
#
#	ssl=[0|1]
#
#	OPTIONAL: If the source instance requires the use of SSL, default is 0
#
#
# Beyond these 3 variables, all other variables passed to the URL of this script will be 
#   interpretted as filters.  For instance, you can filter on the source list profileId
#   by adding profileId=3 to the URL.
#
#   Using profileId would be equivalent to support what the "python" version of 
#   RadarrSync does, but you can optionally create filters on other items, too.

header( "Content-type:application/json\n" );

$instance = $_GET['instance'];
$apikey = $_GET['apikey'];
$usessl = ( array_key_exists( 'ssl', $_GET ) ? $_GET['ssl'] : 0 );

foreach( array( 'instance', 'apikey', 'ssl' ) as $v ) {
	unset( $_GET[$v] );
}

if( !isset( $instance ) || !isset( $apikey ) ) {
	http_response_code( 400 );
	exit;
}

#
# build out instance variable
$instance = 'http' . ( $usessl ? 's' : '' ) . '://' . $instance . '/api/movie?apikey=' . $apikey;

$results = json_decode( file_get_contents( $instance ) );
if( !isset( $results ) || count( $results ) == 0 ) {
	http_response_code( 502 );
	exit;
}

$output = array();

foreach( $results as &$movie ) {
	$match = 1;
	foreach( $_GET as $key => $value ) {
		if( $movie->$key != $value ) {
			$match = 0;
			break;
		}
	}
	if( $match ) array_push( $output, array(
		'id'	=> $movie->tmdbId,
		'title'	=> $movie->title,
		'status' => $movie->status,
	) );
}

print( json_encode( $output ) );

?>

