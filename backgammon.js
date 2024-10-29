window.fhk_backgammon_jso = {
  diagnostics: false,
  the_hbeat: { 'timer': null },
  heartbeat: function() {
    try { clearInterval(window.fhk_backgammon_jso.the_hbeat.timer); } catch(e) {};
    window.fhk_backgammon_jso.the_hbeat.timer = setInterval(function() {
      var oo = {};
      oo.p = 'p';
      window.fhk_backgammon_jso.callajaxwithpost('/wp-admin/admin-ajax.php?action=backgammon_fhk_heartbeatupdate',oo,window.fhk_backgammon_jso.ajaxcallback);
    }, 10000); // 10 seconds
  },

  callajaxwithpost: function(url,oo,cb) {
    var xmlhttp = new XMLHttpRequest();
    xmlhttp.onreadystatechange = function() {
      if (xmlhttp.readyState == XMLHttpRequest.DONE) {
        if (xmlhttp.status == 200) cb(xmlhttp);
        else if (xmlhttp.status == 400) { try { console.debug('There was an error 400'); } catch(e) {}; }
        else { try { console.debug('Non-200 error code returned, code ' + xmlhttp.status); } catch(e) {}; }
      }
    };
    xmlhttp.open('POST', url, true);
    xmlhttp.setRequestHeader('Content-type', 'application/x-www-form-urlencoded');
    xmlhttp.send('ajaxdata=' + encodeURIComponent(JSON.stringify(oo)));
  },

  ajaxcallback: function(result) {
    if ( window.fhk_backgammon_jso.diagnostics === true ) {
      try { console.debug('result=',result); } catch(e) {};
      try { console.debug('result.response=',JSON.parse(result.response)); } catch(e) {};
    }
  },

  clicktostart: function(o,evt) {
    var d = o.closest('.fhk_backgammon_plugin'); if ( ! d ) return;
    var n = 0;
    if ( n == 0 && (evt.offsetX/o.clientWidth) < 0.5000 && (evt.offsetY/o.clientHeight) < 0.5000 ) n = 1;
    if ( n == 0 && (evt.offsetX/o.clientWidth) > 0.5000 && (evt.offsetY/o.clientHeight) < 0.5000 ) n = 2;
    if ( n == 0 && (evt.offsetX/o.clientWidth) < 0.5000 && (evt.offsetY/o.clientHeight) > 0.5000 ) n = 3;
    if ( n == 0 && (evt.offsetX/o.clientWidth) > 0.5000 && (evt.offsetY/o.clientHeight) > 0.5000 ) n = 4;

    if ( n > 0 && n <= 2 ) document.location.href = 'https://www.simplybg.com/parlour/openplay';
    if ( n > 0 && n == 3 ) {
      d.innerHTML = '<span class="remoteplayerlink">Redirecting to SimplyBG.com as Player 1 ... </span><a id="remoteplayerlink" class="remoteplayerlink" style="display:none" href="https://www.simplybg.com/play/0?remoteplay=player1"></a>';
      setTimeout(function() { document.getElementById('remoteplayerlink').click(); }, 1000); 
    }
    if ( n > 0 && n == 4 ) {
      d.innerHTML = '<span class="remoteplayerlink">Redirecting to SimplyBG.com as Player 2 ... </span><a id="remoteplayerlink" class="remoteplayerlink" style="display:none" href="https://www.simplybg.com/play/0?remoteplay=player2"></a>';
      setTimeout(function() { document.getElementById('remoteplayerlink').click(); }, 1000); 
    }
  }

};

document.addEventListener('DOMContentLoaded', function(){
  try { console.info('hello from the SimplyBG.com backgammon plugin'); } catch(e) {};
  window.fhk_backgammon_jso.heartbeat();
});

