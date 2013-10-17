<?php include('database.php'); ?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="">

    <title>MusicAPP - <?php echo date('Y') ?></title>

    <!-- Bootstrap core CSS -->
    <link href="css/bootstrap.css" rel="stylesheet">
    <link href="css/typeahead.css" rel="stylesheet">
    <link href="css/slider.css" rel="stylesheet">

    <!-- Custom styles for this template -->
    <link href="css/css.css" rel="stylesheet">

    <!-- HTML5 shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!--[if lt IE 9]>
      <script src="../../assets/js/html5shiv.js"></script>
      <script src="../../assets/js/respond.min.js"></script>
    <![endif]-->
  </head>

  <body>

    <!-- Fixed navbar -->
    <div class="navbar navbar-default navbar-fixed-top">
      <div class="container">
        <div class="navbar-collapse collapse">  
          <form class="navbar-form">
            <div class="form-group col-md-8">
              <input id="musicAC" type="text" placeholder="O que deseja ouvir hoje...?" class="typeahead form-control">
            </div>
            <div class="col-md-4">
              <a data-toggle="modal" href="#modalAdd" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> Adicionar Álbum</a>
              <a data-toggle="modal" href="#modalAdd2" class="btn btn-primary"><i class="glyphicon glyphicon-plus-sign"></i> Adicionar Música</a>
            </div>
          </form>
        </div><!--/.nav-collapse -->
      </div>
    </div>
    <?php $albums = mysql_query('SELECT * FROM albums ORDER BY artist_id DESC, id DESC'); ?>
    <div class="container">
      <?php while ($album = mysql_fetch_array($albums)) { ?>
        <div class="col-md-3">
          <div class="flip">
            <div>
              <button onclick="playAlbum(<?php echo $album['id']; ?>);" type="button" class="btn btn-primary btn-lg btn-block"><i class="glyphicon glyphicon-play"></i> Tocar</button>              
              <button onclick="playAlbum(<?php echo $album['id']; ?>, true);" type="button" class="btn btn-primary btn-lg btn-block"><i class="glyphicon glyphicon-plus"></i> Adicionar</button>
              <button onclick="modalAlbum(<?php echo $album['id']; ?>);" type="button" class="btn btn-primary btn-lg btn-block"><i class="glyphicon glyphicon-plus-sign"></i> Músicas</button>
              <button onclick="editAlbum(<?php echo $album['id']; ?>);" type="button" class="btn btn-primary btn-lg btn-block"><i class="glyphicon glyphicon-pencil"></i> Editar</button>
            </div>
            <div>
              <?php 
                $albumImg = 'img/albums/'.$album['artist_id'].'/'.$album['id'].'.jpg';
                if (!file_exists($albumImg)) {
                  $albumImg = 'img/sem-imagem.jpg';
                }
              ?>
              <img src="<?php echo $albumImg; ?>" width="220" height="220" />
            </div>
          </div>
        </div>
      <?php } ?>
    </div> <!-- /container -->

    <div class="modal fade" id="myModal">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
            <h4 class="modal-title" id="albumTitle"></h4>
          </div>
          <div class="modal-body">
            <table id="albumSongs" class="table table-hover">
              <tr>
                <th>#</th>
                <th>Nome</th>
                <th>Duração</th>
                <th colspan="2">Ações</th>
              </tr>
            </table>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
          </div>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modalAdd">
      <div class="modal-dialog">
        <div class="modal-content">
          <form class="navbar-form" name="formImport" action="importAlbum.php" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Adicionar Álbum</h4>
            </div>
            <div class="modal-body">              
              <div class="form-group">
                <label for="artist">Banda/Cantor</label>
                <input type="artist" name="artist" class="form-control" id="artist" />
              </div>
              <div class="form-group">
                <label for="album">Álbum</label>
                <input type="album" name="album" class="form-control" id="album" />
              </div>
              <div class="form-group">
                <label for="capa">Capa do Álbum</label>
                <input type="file" name="capa" class="form-control" id="capa" />
                <label for="songs">Músicas</label>
                <input type="file" name="songs[]" class="form-control" multiple id="songs" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Importar Álbum</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" id="modalAdd2">
      <div class="modal-dialog">
        <div class="modal-content">
          <form class="navbar-form" name="formImport" action="importSongs.php" method="POST" enctype="multipart/form-data">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
              <h4 class="modal-title">Adicionar Músicas</h4>
            </div>
            <div class="modal-body">              
              <div class="form-group col-md-12" id="music0">
                <label for="artist">Música</label>
                <input type="file" name="musics[]" multiple="multiple" class="form-control"/>                
              </div>
            </div>
            <div class="modal-footer">
              <button type="submit" class="btn btn-primary">Importar Músicas</button>
              <button type="button" class="btn btn-default" data-dismiss="modal">Fechar</button>
            </div>
          </form>
        </div><!-- /.modal-content -->
      </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <!-- Fixed navbar -->
    <div id="bottom" class="navbar navbar-default navbar-fixed-bottom">
      <div class="container">
        <div class="navbar-collapse collapse">  
          <form class="navbar-form">
            <div class="form-group col-md-12">
              <div class="col-md-5">
                <a id="playBtn" href="javascript:;"><i class="glyphicon glyphicon-play"></i></a>
                <a id="pauseBtn" href="javascript:;"><i class="glyphicon glyphicon-pause"></i></a>
                <a id="stopBtn" href="javascript:;"><i class="glyphicon glyphicon-stop"></i></a>
                <a id="backBtn" href="javascript:;"><i class="glyphicon glyphicon-fast-backward"></i></a>
                <a id="nextBtn" href="javascript:;"><i class="glyphicon glyphicon-fast-forward"></i></a>
                <em id="songTitle"></em>
              </div>
              <div class="col-md-4">
                <div class="progress">
                  <div class="progress-bar progress-bar-info" role="progressbar" id="current" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></div>
                </div>
              </div>
              <div class="col-md-3">
                <a rel="popover" data-placement="top" href="javascript:;" id="volumeBtn"><i id="volumeBtnIcon" class="glyphicon glyphicon-volume-up"></i></a>
                <a href="javascript:;" onClick="openPlaylist();"><i class="glyphicon glyphicon-plus-sign"></i></a>                
              </div>
            </div>
          </form>
        </div><!--/.nav-collapse -->
      </div>
    </div>

    <div id="popoverVolume">
      <input type="text" id="volumeSlider" class="span2" value="" data-slider-min="0" data-slider-max="1" data-slider-step="0.005" data-slider-value="1" data-slider-selection="after" data-slider-tooltip="hide">
    </div>


    <!-- Bootstrap core JavaScript
    ================================================== -->
    <!-- Placed at the end of the document so the pages load faster -->
    <script src="js/jquery.js"></script>
    <script src="js/bootstrap.min.js"></script>
    <script src="js/js.js"></script>
    <script src="js/bootstrap-slider.js"></script>
    <script src="js/typeahead.js"></script>
    <script src="js/hogan.js"></script>
  </body>
</html>
