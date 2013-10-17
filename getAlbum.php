<?php
	include('database.php');

	$id = $_GET['id'];
	$album = mysql_fetch_array(mysql_query('SELECT * FROM albums INNER JOIN artists ON albums.artist_id = artists.id WHERE albums.id = '.mysql_escape_string($id)));
	$songs = mysql_query('SELECT * FROM songs WHERE album_id = '.mysql_escape_string($id).' ORDER BY number ASC');
	if (!empty($album)) {
		$album['songs'] = array();
		while($song = mysql_fetch_array($songs)){
			$album['songs'][] = $song;
		}
		echo json_encode($album);
	}	