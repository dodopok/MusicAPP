<?php
	include('database.php');
	include('include/getid3/getid3.php');
	if(!empty($_FILES)){
		$getID3 = new getID3;
		foreach ($_FILES['musics']['name'] as $key => $nome) {
			$info = $getID3->analyze($_FILES['musics']['tmp_name'][$key]);
			getid3_lib::CopyTagsToComments($info);

			$exist = mysql_fetch_array(mysql_query('SELECT id as id FROM artists WHERE artist = "'.mysql_escape_string($info['comments']['artist'][0]).'"'));
			if(empty($exist['id'])){
				mysql_query('INSERT INTO artists(artist) VALUES ("'.mysql_escape_string($info['comments']['artist'][0]).'")');
				$artist_id = mysql_insert_id();
			} else {
				$artist_id = $exist['id'];
			}

			$exist = mysql_fetch_array(mysql_query('SELECT id as id FROM albums WHERE album = "'.mysql_escape_string($info['comments']['album'][0]).'"'));
			if(empty($exist['id'])){
				mysql_query('INSERT INTO albums(artist_id, album) VALUES ("'.mysql_escape_string($artist_id).'" ,"'.mysql_escape_string($info['comments']['album'][0]).'")');
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

			$exist = mysql_fetch_array(mysql_query('SELECT id as id FROM songs WHERE song = "'.mysql_escape_string($info['comments']['title'][0]).'" AND album_id = "'.$album_id.'"'));
			if(empty($exist['id'])){
				mysql_query('INSERT INTO songs(number, song, ext, album_id, duration) VALUES ("'.mysql_escape_string($info['comments']['track'][0]).'", "'.mysql_escape_string($info['comments']['title'][0]).'", "'.mysql_escape_string($info['fileformat']).'", "'.mysql_escape_string($album_id).'", "00:0'.$info['playtime_string'].'")');
				$dir = 'mp3/'.$artist_id.'/';
				if(!file_exists($dir)){
					mkdir($dir);				
				}
			}

			$dir = 'mp3/'.$artist_id.'/'.$album_id;
			if(!file_exists($dir)){
				mkdir($dir);				
			}
			move_uploaded_file($_FILES['musics']['tmp_name'][$key], $dir.'/'.$info['comments']['track'][0].'.'.$info['fileformat']);
		}

		header('Location: index.php');
	}