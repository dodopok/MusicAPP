var music = new Audio();
var playlist = [];

$(document).ready(function(){
	$('#volumeBtn').popover({
		html : true, 
		content: $('#popoverVolume').html(),
	});	

	$('#popoverVolume').hide();

	$('#pauseBtn').hide();
	music.addEventListener('timeupdate',function (){
    	curtime = (parseInt(music.currentTime)/music.duration)*100;
       	updateBar(curtime);
    });

	$('#musicAC').typeahead({
	  name: 'musicas',
      remote: "completeSongs.php?song=%QUERY",
	  template: '<p><strong>{{value}}</strong> – {{artist}}</p>',                                                             
	  engine: Hogan
	}).on('typeahead:opened',function(){$('.tt-dropdown-menu').css('width',$(this).width() + 'px');}).on('typeahead:selected', function (e, datum) {
	    getPlaySong(datum.id);
	});

});

$(music).on('ended', function(){
	next = playlist.shift();
	if(next != undefined){
		getPlaySong(next);
	}
});

$('#nextBtn').on('click', function(){
	next = playlist.shift();
	if(next != undefined){
		getPlaySong(next);
	}
});

$('#backBtn').on('click', function(){
	back = playlist.shift();
	if(next != undefined){
		getPlaySong(back);
	}
});

$('#volumeBtn').on('shown.bs.popover', function () {
	$('#volumeSlider').slider();
	$('#volumeSlider').on('slide', function(ev){
		music.volume = ev.value;
	});
})

$('#playBtn').on('click', function(){
	play();
	$('#current').attr('aria-valuemax', music.duration);
});

$('#pauseBtn').on('click', function(){
	pause();
});

$('#stopBtn').on('click', function(){
	stop();
});

$('#volumeBtn').on('hover', function(){
	$('#volumeBtn').popover('show');
});

var contMusic = 0;

$('#moreMusic').on('click', function(){
	contMusic++;
	$('#music'+(contMusic-1)).clone().attr('id', 'music'+contMusic).insertAfter($('#music'+(contMusic-1)));
});

function getPlaySong(id){
	$.getJSON('getSong.php', {id: id}, function(data){
		console.debug(data);
		artist = data.artist_id;
		album = data.album_id;
		number = data.number;
		ext = data.ext;
		load(artist, album, number, ext);
		$('#songTitle').text(data.artist+' - '+data.song);
	});
}

function updateBar(time){
	$("#current").attr("aria-valuenow", time);
    $("#current").css("width", time+'%');
}

function load(artist, album, number, ext){
	music.src = '/musicapp/mp3/'+artist+'/'+album+'/'+number+'.'+ext;
	play();	
}

function playAlbum(id, onlyAdd){
	$.getJSON('getAlbum.php', {id: id}, function(data){
		if(onlyAdd != true){
			playlist = [];
		}
		for(var i in data.songs){			
			playlist.push(data.songs[i].id);			
		}
		if(onlyAdd != true){
			playlistStart();
		}
	});
}

function addToPlaylist(id){
	playlist.push(id);
}

function playlistStart(){
	music.pause();
	getPlaySong(playlist.shift());
}

function modalAlbum(id){
	$.getJSON('getAlbum.php', {id: id}, function(data){
		$('#albumTitle').html('<i class="glyphicon glyphicon-align-justify"></i> '+data.artist+' - '+data.album);
		$('.songTr').remove();
		for(var i in data.songs) {
			song = data.songs[i];
			songHtml = '';
			songHtml += '<tr class="songTr">';
	        	songHtml += '<td data-type="number">'+song.number+'</td>';
	            songHtml += '<td data-type="songTitle" data-artist="'+data.artist+'">'+song.song+'</td>';
	            songHtml += '<td data-type="duration">'+song.duration+'</td>';
	            songHtml += '<td data-type="controls">';
	            	songHtml += '<a href="javascript:;" data-type="song" data-number="'+song.number+'" data-artist="'+data.artist_id+'" data-album="'+song.album_id+'" data-ext="'+song.ext+'"><i class="glyphicon glyphicon-play"></i></a>';
	            songHtml += '</td>';
	            songHtml += '<td>';
	            	songHtml += '<a href="javascript:;" onClick="addToPlaylist('+song.id+');"><i class="glyphicon glyphicon-plus"></i></a>';
	            songHtml += '</td>';
	        songHtml += '</tr>';
	        $('#albumSongs').append(songHtml);
		}
		$('[data-type="song"]').on('click', function() {		
			artist = $(this).attr('data-artist');
			album = $(this).attr('data-album');
			number = $(this).attr('data-number');
			ext = $(this).attr('data-ext');
			load(artist, album, number, ext);
			songTitle = $(this).parent().parent().find('td[data-type="songTitle"]');
			$('#songTitle').text($(songTitle).attr('data-artist')+' - '+$(songTitle).text());
		});

		$('#myModal').modal('show');
	});
}

function play(){
	if(music.src != ''){
		music.play();
		$('#playBtn').hide();
		$('#pauseBtn').show();
	}
}

function pause(){
	music.pause();
	$('#pauseBtn').hide();
	$('#playBtn').show();
}

function stop(){
	if(music.src != ''){
		music.pause();	
		music.currentTime = 0;
		updateBar(0);
		$('#pauseBtn').hide();
		$('#playBtn').show();
	}
}

function mute(){
	music.volume = 0;
}

function unmute(){
	music.volume = 1;
}