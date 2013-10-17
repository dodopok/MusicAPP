<?php
	include('database.php');

	$id = $_GET['id'];
	$song = mysql_fetch_array(mysql_query('SELECT * FROM songs INNER JOIN albums ON songs.album_id = albums.id INNER JOIN artists ON albums.artist_id = artists.id WHERE songs.id = '.mysql_escape_string($id)));
	echo json_encode($song);