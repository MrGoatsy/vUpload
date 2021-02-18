document.addEventListener('DOMContentLoaded', () => { 
    // This is the bare minimum JavaScript. You can opt to pass no arguments to setup.
    const player = new Plyr('#player');
    
    // Expose
    window.player = player;
  
    // Bind event listener
    function on(selector, type, callback) {
      document.querySelector(selector).addEventListener(type, callback, false);
    }
});