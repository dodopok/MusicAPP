<?php
	include('database.php');
	if(!empty($_POST)){
		$exist = mysql_fetch_array(mysql_query('SELECT id as id FROM artists WHERE artist = "'.mysql_escape_string($_POST['artist']).'"'));
		if(empty($exist['id'])){
			mysql_query('INSERT INTO artists(artist) VALUES ("'.mysql_escape_string($_POST['artist']).'")');
			$artist_id = mysql_insert_id();
		} else {
			$artist_id = $exist['id'];
		}

		$exist = mysql_fetch_array(mysql_query('SELECT id as id FROM albums WHERE album = "'.mysql_escape_string($_POST['album']).'"'));
		if(empty($exist['id'])){
			mysql_query('INSERT INTO albums(artist_id, album) VALUES ("'.mysql_escape_string($artist_id).'" ,"'.mysql_escape_string($_POST['album']).'")');
			$album_id = mysql_insert_id();
		} else {
			$album_id = $exist['id'];
		}

		if(!empty($_FILES['capa'])){
			$dir = 'img/albums/';
			if(!file_exists($dir.$artist_id)){
				mkdir($dir.$artist_id);				
			}
			move_uploaded_file($_FILES['capa']['tmp_name'], $dir.$artist_id.'/'.$album_id.'.jpg');
		}

		foreach ($_FILES['songs']['name'] as $key => $song) {
			$song = explode(' - ', $song);
			$song[1] = str_replace('.mp3', '', $song[1]);
			mysql_query('INSERT INTO songs(number, song, ext, album_id, duration) VALUES ("'.mysql_escape_string($song[0]).'", "'.mysql_escape_string($song[1]).'", "mp3", "'.mysql_escape_string($album_id).'", "00:0'.rand(0, 4).':'.str_pad(rand(00, 59), 2, "0", STR_PAD_LEFT).'")');
			$dir = 'mp3/'.$artist_id.'/';
			if(!file_exists($dir)){
				mkdir($dir);				
			}
			$dir = 'mp3/'.$artist_id.'/'.$album_id;
			if(!file_exists($dir)){
				mkdir($dir);				
			}
			move_uploaded_file($_FILES['songs']['tmp_name'][$key], $dir.'/'.(int)$song[0].'.mp3');
		}

		header('Location: index.php');
	}