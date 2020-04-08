/**
 * Main Javascripts
 */
var hands = {
      'hour': $('#hourHand'),
      'minute': $('#minuteHand'),
      'second': $('#secondHand')
    };

updateClock();

function updateClock(){
  var dateObj = new Date(),
    current = {
      'hour': dateObj.getHours(),
      'minute': dateObj.getMinutes(),
      'second': dateObj.getSeconds()
    };
  
  translate(current, 'hour');
  translate(current, 'minute');
  translate(current, 'second');
  setTimeout(function(){ 
    updateClock();
  }, 1000);
}

function translate(obj,type){
  if($.type(obj) === 'object'){
    if(type === 'hour'){
      var wholeHr = ((parseInt(obj.hour) * 360) / 12),
          partHr = ((parseInt(obj.minute) * 30) / 60),
          theHour = parseInt(wholeHr) + parseInt(partHr);
      hands.hour.css('transform','rotate(' + theHour + 'deg)');
    } else if(type === 'minute')
      hands.minute.css('transform','rotate(' + (parseInt(obj.minute) * 360) / 60 + 'deg)');
    else if(type === 'second')
      hands.second.css('transform','rotate(' + (parseInt(obj.second) * 360) / 60 + 'deg)');
  }
}

/**
 * Ajax Functionality for Time Traveler
 */
jQuery(document).ready(function($){
  $('#time_travel').click(function(){
    // jsY, jyM, jsD, tmach=1|0
    var toTime    = $('.toTime'),
        outputMsg = '',
        loopError = false,
        ajax_json = { 'tmach' : '1' },
        html      = '';

    for(var x=0; x<toTime.length; x++){
      var cur     = toTime[x],
          $cur    = $(cur),
          disName = cur.id.substring(4);

      // check value to see if time travel is possible
      if(cur.value=='0'){
        outputMsg += 'Error: a '+disName+' must be chosen to time travel\n';
        loopError = true;
      } else {
        ajax_json[cur.id] = cur.value;
      }
    }

    if(loopError){
      alert(outputMsg);
    } else {
      // all times gathered, wrap into ajax and send
      $.get('index.php',ajax_json,function(data){
        data = $.parseJSON(data);

        html += '#zodiac {';
        html += '  -webkit-transform: rotate(-'+data.DoY+'deg);';
        html += '  transform: rotate(-'+data.DoY+'deg); }';
        html += '#clock:after { background: '+data.msprite+'; }';

        $('#dynamicCSS').html(html);
        console.log(data); // this outputs json
      });
    }
  });

  $('.external').click(function(){
    window.open(this.href);
    return false;
  })
});