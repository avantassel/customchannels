/*
  
*/
class CustomChannels {
  
  static urls = {
    stream: 'https://stream.customchannels.net/dev_test_96.mp3',
    nowplaying: 'https://lambda.customchannels.rocks/nowplaying?url=http://stream.customchannels.net/dev_test_96.mp3',
    recent: 'recent.php'
  };
  
  constructor(audio){
    this.audio = audio;
    this.gettingRecent = false;
    this.recentlyPlayed = [];
    this.recentlyPlayedPagination = {};
    this.nowPlaying = {};
    this.state = 'paused';
    this.source = 'stream';
    this.setupAudio();
  }
  
  setupAudio = () => {
    this.audio.addEventListener('pause', (event) => {
      this.state = 'paused';
    });  
    this.audio.addEventListener('play', (event) => {
      this.state = 'playing';
    });
  }
  
  setNotified = () => {
    this.nowPlaying.notified = true;  
  }
  
  playStream = () => {
    if (this.source == 'sample' || this.audio.paused) {
      this.source = 'stream';
      this.audio.setAttribute('src', CustomChannels.urls.stream);
      this.audio.play();
    } else {
      this.audio.pause();
    }
    return this.getNowPlaying(); 
  }
  
  playSample = (id) => {
    if (this.source == 'stream' || this.audio.paused || this.nowPlaying.id != id) { 
      this.source = 'sample';
      const recent = this.recentlyPlayed.filter(r => r.id == id);
      if (recent.length) {
        this.nowPlaying = recent[0];
        this.audio.setAttribute('src', recent[0].sample_url);        
        this.audio.play();
      }
    } else {
      this.audio.pause();
    }
    return Promise.resolve(true);
  }
  
  playNextSample = () => {
    this.audio.pause();
    const index = this.recentlyPlayed.findIndex(r => r.id == this.nowPlaying.id);
    if (this.recentlyPlayed[index + 1]) {
      return this.playSample(this.recentlyPlayed[index + 1].id);
    }
    // repeat
    return this.playSample(this.recentlyPlayed[0].id);
  }
  
  getNowPlaying = () => {
    if (this.source == 'sample')
      return Promise.resolve(true);
    return fetch(CustomChannels.urls.nowplaying)
    .then((response) => response.json())
    .then((data) => {
      if(data.track){
        // parse track data
        const track = data.track.split('-');
        if (track.length) {
          const artist = track[0].trim();
          const title = track[1].trim();
          let recent = this.recentlyPlayed.filter(r => r.title == title && r.artist == artist);
          if(!recent.length)
            recent = this.recentlyPlayed.filter(r => r.title == title);
          if(!recent.length)
            recent = this.recentlyPlayed.filter(r => r.artist == artist);
          if (recent.length && recent[0].id != this.nowPlaying.id) {
            this.nowPlaying = {
              ...recent[0]
            };
          }
        }
      }
      return Promise.resolve(true);
    }).catch(error => {
      console.error(error);
    });
  }
  
  getRecent = () => {
    if (this.gettingRecent)
      return Promise.reject(true);
    
    return fetch(CustomChannels.urls.recent)
      .then((response) => response.json())
      .then((response) => {
        this.updateRecentlyPlayed(response.data);
        this.recentlyPlayedPagination = response.pagination;
        this.gettingRecent = false;
        return Promise.resolve(true);
      }).catch(error => {
        this.gettingRecent = false;
        console.error(error);
      });
  }
  
  updateRecentlyPlayed = (data) => {
    data.map(d => { 
      if(!this.recentlyPlayed.filter(r => r.id == d.id).length)
        this.recentlyPlayed.push(d);
    });
  }
  
}
