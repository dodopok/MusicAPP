<?php
	include('database.php');

	$song = $_GET['song'];
	$songDB = mysql_query('SELECT songs.id, artist, song AS value FROM songs INNER JOIN albums ON songs.album_id = albums.id INNER JOIN artists ON albums.artist_id = artists.id WHERE songs.song LIKE "%'.mysql_escape_string($song).'%" OR artists.artist LIKE "%'.mysql_escape_string($song).'%"  COLLATE utf8_general_ci;');
	$songs = array();
	while($song = mysql_fetch_array($songDB)){
		$songs[] = $song;
	}
	echo json_encode($songs);