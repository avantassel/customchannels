<html>
  <head>
    <title>Custom Channels Player</title>
    <link rel="stylesheet" href="styles.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.8/semantic.min.css" integrity="sha512-pbLYRiE96XJxmJgF8oWBfa9MdKwuXhlV7vgs2LLlapHLXceztfcta0bdeOgA4reIf0WH67ThWzA684JwkM3zfQ==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/Glide.js/3.5.0/css/glide.core.css" integrity="sha512-kcsVKF2zQWxpZox0QJTl40HBAhKLjfcUFw2LoTdHilSuHeOSg8uo8zf6ZiIUSHgHHl0H8zRkMcqJz2w4ZH57KA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
  </head>
  <body class="inverted">
    
  <div class="ui placeholder segment sticky inverted">   
  <button class="ui icon button inverted" onclick="toggleMode();" data-content="Toggle Dark Mode">
    <i class="adjust icon big"></i>
  </button>
  <div class="ui active dimmer">
    <div class="ui text loader">Loading</div>
  </div>
  <div class="ui two column stackable center aligned grid">  
    <div class="middle aligned row">
      <div class="column">
        <div class="ui icon header inverted">
        <i class="music icon"></i> Custom Channels Player <br/><br/>      
        <audio id="player" controls src="" type="audio/mpeg">
          Your browser does not support the
          <code>audio</code> element.
        </audio>      
      </div>
      <div id="mainPlayBtn" class="ui primary button inverted" onclick="playStream();">Play Stream</div>  
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
    <div class="extra content">
      <span class="right floated source">
        Stream
      </span>      
    </div>   
  </div>
  </div>
</div>
</div>
</div>

<div id="recent" class="ui four link doubling cards inverted">  
</div>

<script src="stream.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/fomantic-ui/2.8.8/semantic.min.js" integrity="sha512-t5mAtfZZmR2gl5LK7WEkJoyHCfyzoy10MlerMGhxsXl3J7uSSNTAW6FK/wvGBC8ua9AFazwMaC0LxsMTMiM5gg==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>    
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js" integrity="sha512-qTXRIMyZIFb8iQcfjXWCO8+M5Tbc38Qi5WzdPOYZHIlZpzBHG3L3by84BBBOiRGiEb7KKtAOAs5qYdUiZiQNNQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script>
  let darkMode = true;
  const audio = document.getElementById('player');
  const recent = document.getElementById('recent');
  const nowplaying = document.getElementById('nowplaying');
  const customChannels = new CustomChannels(audio);
  
  audio.addEventListener('ended', (event) => {
    if(customChannels.source == 'sample'){
      customChannels.playNextSample()
      .then(() => {
        setTimeout(updateNowPlaying, 400);
      });
    }
  });
  
  $(document).ready(() => {
    $('.ui.sticky').sticky();
    $('.button').popup();
  });
  
  function toggleMode(ele){
    darkMode = !darkMode;
    if(darkMode){
      $(recent).addClass('inverted');
      $(nowplaying).addClass('inverted');
      $('.button').addClass('inverted');
      $('.segment').addClass('inverted');
      $('.header').addClass('inverted');
      $('body').addClass('inverted');
    } else {
      $(recent).removeClass('inverted');
      $(nowplaying).removeClass('inverted');
      $('.button').removeClass('inverted');
      $('.segment').removeClass('inverted');
      $('.header').removeClass('inverted');
      $('body').removeClass('inverted');
    }
  }
  
  function clearPlayButtons(){
    $('.sample').removeClass('red').removeClass('stop').addClass('play');
  }
  
  function updateNowPlaying(){
    $('i.sample').removeClass('red').removeClass('stop').addClass('play');
    if(customChannels.state == 'paused'){
      $(nowplaying).addClass('blur');  
      $('#mainPlayBtn').html('Play Stream');
      $(nowplaying).find('#title').html('Click Play Stream or sample with the play icon.');
      $(nowplaying).find('#artist').html('');
      $(nowplaying).find('#album').html('');
      $(nowplaying).find('#image').hide();
      return;
    }
    
    if(customChannels.source == 'stream')
      $('#mainPlayBtn').html('Pause Stream');
    else {
      $('#mainPlayBtn').html('Play Stream');
      $('#'+customChannels.nowPlaying.id+' i.sample').addClass('red').addClass('stop').removeClass('play');
    }
      
    $(nowplaying).find('.source').html(customChannels.source);
    $(nowplaying).removeClass('blur');
    $(nowplaying).find('#artist').html(customChannels.nowPlaying.artist);
    $(nowplaying).find('#title').html(customChannels.nowPlaying.title);
    $(nowplaying).find('#album').html(customChannels.nowPlaying.album);
    if(customChannels.nowPlaying.album_art && customChannels.nowPlaying.album_art.small)
      $(nowplaying).find('#image').prop('src', customChannels.nowPlaying.album_art.small).show();    
    else
      $(nowplaying).find('#image').hide();
    let message = customChannels.source == 'sample' ? '<label class="ui green label">Sample</label>' : '<label class="ui blue label">Live</label>';    
    
    if(customChannels.state != 'paused' && customChannels.nowPlaying.title && !customChannels.nowPlaying.notified){
      $('body').toast({
        message: `${message} Playing ${customChannels.nowPlaying.title} by ${customChannels.nowPlaying.artist}`,
        class: darkMode ? 'inverted' : ''
      });       
      customChannels.setNotified();   
    }
    $(nowplaying).removeClass('loading');
  }
  
  function updateRecentlyPlayed(){
    let init = !$(recent).children().length;
    customChannels.recentlyPlayed.map(d => {
      if(!$(recent).find('#'+d.id).length){
        if(init)
          $(recent).append(cardHtml(d));
        else
          $(recent).prepend(cardHtml(d));
      }
    });
    $('.dimmer').hide();
    
    // get recent every 20 seconds for buffer
    // not ideal but since we don't have song length and current length of now playing we have to poll
    setTimeout(() => {
      updateRecent();
    }, 20000);
    return true;
  }
  
  function cardHtml(recent){
    return '<div id="'+recent.id+'" class="card" onclick="playSample('+recent.id+')">\
          <div class="image">\
            <img src="'+recent.album_art.large+'">\
          </div>\
          <div class="content">\
            <div class="header">'+recent.artist+'</div>\
            <div class="meta">\
              '+recent.title+' <i class="play circle icon large sample" onclick="playSample('+recent.id+')"></i>\
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
  
  function updateRecent(){
    customChannels.getRecent()
    .then(customChannels.getNowPlaying)
    .then(updateRecentlyPlayed)
    .then(() => {
      setTimeout(updateNowPlaying, 400);
    });
  }
  
  function playStream(){
    customChannels.playStream()
    .then(() => {
      setTimeout(updateNowPlaying, 400);
    });
  }
  
  function playSample(id){
    customChannels.playSample(id)
    .then(() => {
      setTimeout(updateNowPlaying, 400);
    });
  }
  
  updateRecent();
</script>
</body>
</html>