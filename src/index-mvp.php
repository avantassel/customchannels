<html>
  <head>
    <title>Custom Channels Player</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.8/semantic.min.css" integrity="sha512-pbLYRiE96XJxmJgF8oWBfa9MdKwuXhlV7vgs2LLlapHLXceztfcta0bdeOgA4reIf0WH67ThWzA684JwkM3zfQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.5.0/css/glide.core.css" integrity="sha512-kcsVKF2zQWxpZox0QJTl40HBAhKLjfcUFw2LoTdHilSuHeOSg8uo8zf6ZiIUSHgHHl0H8zRkMcqJz2w4ZH57KA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body class="inverted">
    
  <div class="ui placeholder stacked segment sticky inverted">   
  <button class="ui icon button inverted" onclick="toggleMode();" data-content="Toggle Dark Mode">
    <i class="adjust icon large"></i>
  </button>
  <div class="ui active dimmer">
    <div class="ui text loader">Loading</div>
  </div>
  <div class="ui two column center aligned grid">  
    <div class="middle aligned row">
      <div class="column">
        <div class="ui icon header inverted">
        <i class="music icon"></i> Custom Channels Player <br/><br/>      
        <audio id="player" controls src="https://stream.customchannels.net/dev_test_96.mp3" type="audio/mpeg">
          Your browser does not support the
          <code>audio</code> element.
        </audio>      
      </div>
      <div id="mainPlayBtn" class="ui primary button inverted" onclick="playStream(this, 'https://stream.customchannels.net/dev_test_96.mp3');">Play Stream</div>  
  </div>
  <div class="column">
  <!-- Now Playing -->
  <div id="nowplaying" class="ui centered card inverted">
    <div class="content">
      <div class="header inverted">Now Playing</div>
    </div>
    <div class="content">
      <img id="image" class="right floated mini ui image" src="" style="display: none;">
      <div id="title" class="header inverted" style="text-align: left">        
      </div>
      <div id="artist" class="meta" style="text-align: left">        
      </div>
      <div id="album" class="description" style="text-align: left">        
      </div>
    </div>      
  </div>
  </div>
</div>
</div>
</div>

<div id="recent" class="ui four link stackable cards inverted">  
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.8/semantic.min.js" integrity="sha512-t5mAtfZZmR2gl5LK7WEkJoyHCfyzoy10MlerMGhxsXl3J7uSSNTAW6FK/wvGBC8ua9AFazwMaC0LxsMTMiM5gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>    
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
  const audio = document.getElementById('player');
  let recentlyPlayed = [];
  let recentlyPlayedPagination = {};
  let darkMode = true;
  let gettingRecent = false;
  let nowPlaying = {
    sample: false    
  };
  audio.addEventListener('ended', (event) => {
    if(nowPlaying.sample){          
      clearPlayButtons();
      let nextSample = $('#'+nowPlaying.id).next().find('.sample');
      if(nextSample)
        nextSample.click();
    }
  });
  audio.addEventListener('pause', (event) => {
    clearPlayButtons();    
    if(!nowPlaying.sample)
      $('#mainPlayBtn').html( $('#mainPlayBtn').html().replace('Pause','Play') );        
  });  
  audio.addEventListener('play', (event) => {
    if(!nowPlaying.sample)
      $('#mainPlayBtn').html( $('#mainPlayBtn').html().replace('Play','Pause') );    
  });
  
  $('.ui.sticky').sticky();
  
  $('.button').popup();
  
  function toggleMode(ele){
    darkMode = !darkMode;
    if(darkMode){
      $('.button').addClass('inverted');
      $('#recent').addClass('inverted');
      $('#nowplaying').addClass('inverted');
      $('.segment').addClass('inverted');
      $('.header').addClass('inverted');
      $('body').addClass('inverted');
    } else {
      $('.button').removeClass('inverted');
      $('#recent').removeClass('inverted');
      $('#nowplaying').removeClass('inverted');
      $('.segment').removeClass('inverted');
      $('.header').removeClass('inverted');
      $('body').removeClass('inverted');
    }
  }
  
  function clearPlayButtons(){
    $('.sample').removeClass('red').removeClass('stop').addClass('play');
  }
  
  function getNowPlaying(){
    fetch('https://lambda.customchannels.rocks/nowplaying?url=http://stream.customchannels.net/dev_test_96.mp3')
      .then((response) => response.json())
      .then((data) => {
        if(data.track){
          // parse track data
          let track = data.track.split('-');
          if(!nowPlaying.artist || (nowPlaying.artist != track[0].trim() && nowPlaying.title != track[1].trim())){
            // don't update if we are playing samples
            if(nowPlaying.sample && !audio.paused)
              return;
            nowPlaying = {
              id: '',
              artist: track[0].trim(),
              title: track[1].trim(),
              album: '',
            };  
          }
          updateNowPlaying(false);            
        }
      });  
  }
  
  function playStream(ele, url){
    clearPlayButtons();    
    if(audio.paused || nowPlaying.sample){          
      $('#player').prop('src', url);
      audio.play();          
      nowPlaying.sample = false;    
      getNowPlaying();        
    } else {
      audio.pause();
      $('body').toast({
        message: `<label class="ui blue label">Live</label> Pausing stream`,
        class: darkMode ? 'inverted' : ''
      }); 
    }        
  }
  
  function playSample(ele, id){
    let recent = recentlyPlayed.filter(r => r.id == id);
    if($(ele).hasClass('red') && !audio.paused){          
      $('.sample').removeClass('red').removeClass('stop').addClass('play');
      audio.pause();        
    } else if(recent.length){
      nowPlaying = {
        ...recent[0]
      };
      $('.sample').removeClass('red').removeClass('stop').addClass('play');
      $(ele).addClass('red').removeClass('play').addClass('stop');
      $('#player').prop('src', nowPlaying.sample_url);        
      audio.play();      
      $('#mainPlayBtn').html('Play Stream');          
      updateNowPlaying(true);
    }
  }
  
  function updateNowPlaying(sample){
    if(audio.paused){
      $('#nowplaying #artist').html('');
      $('#nowplaying #title').html('');
      $('#nowplaying #album').html('');
      $('#nowplaying #image').hide();
      return;
    }
    if(nowPlaying.title){
      $('#nowplaying').addClass('loading');
    }
    
    let showToast = true;
    nowPlaying.sample = sample;
      
    // search for artist in recent list
    if(!nowPlaying.id && nowPlaying.title && nowPlaying.artist){
      let recent = recentlyPlayed.filter(r => r.title == nowPlaying.title && r.artist == nowPlaying.artist);
      if(!recent.length)
        recent = recentlyPlayed.filter(r => r.title == nowPlaying.title);
      if(!recent.length)
        recent = recentlyPlayed.filter(r => r.artist == nowPlaying.artist);
        
      if(recent.length){
        if(nowPlaying.id != recent[0].id){
          nowPlaying = {
            sample,
            ...recent[0]
          };  
        } else {
          return;
        }
      }
    }
    $('#nowplaying #artist').html(nowPlaying.artist);
    $('#nowplaying #title').html(nowPlaying.title);
    $('#nowplaying #album').html(nowPlaying.album);
    if(nowPlaying.album_art && nowPlaying.album_art.small)
      $('#nowplaying #image').prop('src', nowPlaying.album_art.small).show();    
    else
      $('#nowplaying #image').hide();
    let message = sample ? '<label class="ui green label">Sample</label>' : '<label class="ui blue label">Live</label>';    
    if(showToast && nowPlaying.title){
      $('body').toast({
        message: `${message} Playing ${nowPlaying.title} by ${nowPlaying.artist}`,
        class: darkMode ? 'inverted' : ''
      });          
    }
    $('#nowplaying').removeClass('loading');
  }
  
  function updateRecentlyPlayed(data){
    let init = !recentlyPlayed.length;
    data.map(d => {
      if(!$('#recent #'+d.id).length){
        recentlyPlayed.push(d);
        if(init)
          $('#recent').append(cardHtml(d));
        else
          $('#recent').prepend(cardHtml(d));
      }
    });
    $('.dimmer').hide();
  }
  
  function cardHtml(recent){
    return '<div id="'+recent.id+'" class="card">\
          <div class="image">\
            <img src="'+recent.album_art.large+'">\
          </div>\
          <div class="content">\
            <div class="header">'+recent.artist+'</div>\
            <div class="meta">\
              '+recent.title+' <i class="play circle icon large sample" onclick="playSample(this, '+recent.id+')"></i>\
            </div>\
            <div class="description">'+recent.album+'</div>\
          </div>\
          <div class="extra content played">\
            <span class="right floated">\
              <span class="ago">'+moment.utc(recent.played_at).fromNow()+'</span>\
            </span>\
            <span>\
              <span class="date">'+moment.utc(recent.played_at).format("ddd, MMM Do YYYY, h:mm:ss a")+'</span>\
            </span>\
          </div>\
        </div>';
  }
  
  function getRecent(){
    if(gettingRecent) return;
    gettingRecent = true;
    return fetch('recent.php')
      .then((response) => response.json())
      .then((response) => {
        updateRecentlyPlayed(response.data);
        recentlyPlayedPagination = response.pagination;
        gettingRecent = false;
        // get recent every 20 seconds for buffer
        // not ideal but since we don't have song length and current length of now playing we have to poll
        setTimeout(function() {
          getRecent();
          getNowPlaying();
        }, 20000);
      }).catch(error => {
        gettingRecent = false;
        console.error(error);
      });
  }
  
  getRecent();
</script>
</body>
</html>