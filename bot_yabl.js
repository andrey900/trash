$("#tabsLotteryTypes-2").prepend('<div id="bot-inform">'+
	'<div style="float:left;">'+
	'<p><b>BOT FUNCTIONS</b></p>'+
	'*x: <input id="koef" size="5" value="1.3"><br>'+
	'max: <input id="limit" size="5" value="100"><br>'+
	'1-<input class="logic-select" type="radio" name="log" value="logic1" selected="" >'+
	'2-<input class="logic-select" type="radio" name="log" value="logic2">'+
	'3-<input class="logic-select" type="radio" name="log" value="logic3">'+
	'4-<input class="logic-select" type="radio" name="log" value="logic4">'+
	'<br><button id="startBot">start</button> <button id="stopBot">stop</button>'+
	'</div><div style="float:left;">'+
	'<p><b>BOT Statistics</b></p>'+
	'<p>Max numbers:<span id="b_stat_max"></span></p>'+
	'<p>Min numbers:<span id="b_stat_min"></span></p>'+
	'</div><div style="clear:both;"></div></div>');

function Statistics(){
	this.numbers = Object.create(null);
	
	this.newRound = function(){
		var self = this;
		$("#divWinningNumbers div").each(function(i, e){
			var name = parseInt($(e).text());
			if( !self.numbers[name] ){
				self.numbers[name] = 1;
			} else {
				self.numbers[name]++;
			}
		});

		return this;
	}

	this.setMaxMin = function(){
		var self = this;
		var arr = Object.keys( this.numbers ).map(function ( key ) { return self.numbers[key]; });
		var min = Math.min.apply( null, arr );
		var max = Math.max.apply( null, arr );
		$('#b_stat_max').text(min);
		$('#b_stat_min').text(max);
	}

	this.getStatistics = function(){
		return this.numbers;
	}

	this.clearStatistics = function(){
		this.numbers = Object.create(null);
	}
}

var statistic = new Statistics();

$(document).on('click', '#btPlayInstant', function(){
	statistic.newRound().setMaxMin();
});

$(document).on('click', '#startBot', function(){
	window.t = setInterval(logic, 1000);
	return false;
});

$(document).on('click', '#stopBot', function(){
	clearInterval(window.t);
	return false;
});

//3
function logic(){
	if( !$('#divButtonsPlay2').is(':visible') )
		return;

	var winNumbers = $('#divWinningNumbers .ballHit');
	if( winNumbers.length == 0 ){
		// No winn balls
	    stavka = Math.round(parseFloat($('#tbBetValue').val().replace(",", ".")) * 100000000 * parseFloat($(document).find("#koef").val()), 8);
	    if( stavka > parseInt($(document).find('#limit').val()) )
	    	stavka = 10;
	    stavka = stavka / 100000000;
	    stavka = stavka.toFixed(8).replace(".", ',');
	} else if( winNumbers.length == 1 ){
		// 1 win ball
		stavka = $('#tbBetValue').val();
	} else if( winNumbers.length > 1 ){
		// real winns
	    stavka = '0,00000010';
	}
	
	$('#tbBetValue').val(stavka);
	$('#btPlayInstant').click();
}
//4
function logic(){
	if( !$('#divButtonsPlay2').is(':visible') )
		return;

	var winNumbers = $('#divWinningNumbers .ballHit');
	if( winNumbers.length == 1 ){
		// No winn balls
	    stavka = Math.round(parseFloat($('#tbBetValue').val().replace(",", ".")) * 100000000 * parseFloat($(document).find("#koef").val()), 8);
	    if( stavka > parseInt($(document).find('#limit').val()) )
	    	stavka = 10;
	    stavka = stavka / 100000000;
	    stavka = stavka.toFixed(8).replace(".", ',');
	} else if( winNumbers.length == 0 ){
		// 1 win ball
		stavka = $('#tbBetValue').val();
	} else if( winNumbers.length > 1 ){
		// real winns
	    stavka = '0,00000010';
	}
	
	$('#tbBetValue').val(stavka);
	$('#btPlayInstant').click();
}

var stat = [
  [1, 3, 6, 8, 4, 0],
  [5, 9, 1, 4, 7, 8],
  [3, 5, 8, 7, 2, 0],
];

function recursionItems(items, level, arResult){
	if( !arResult )
		var arResult = [];

	for( var i = 0; i < items.length; i++ ){
		var strReturn = "";
		if( level >= 1 )
			strReturn = recursionItems(items.slice(1), level-1, arResult);
    else
      strReturn = items[i]+","+ items[i+1];

		arResult.push(strReturn);
	}

	return arResult;
}
    
console.log(recursionItems(stat[0], 1));

function recStr(arStr, items){
	for (var s = 0; s < arStr.length; s++) {
		for( var i = 0; i < items.length; i++ ){
			arStr[i] += items[i];
		}
	};

	return arStr;
}

/*

var double = {};
  
function findDouble(items, cnt){
  for(var i = 0; i < items.length;i++ ){
    var n = recursionPar(items, 1);
    if( !double[n] )
      double[n] = 1;
    else
      double[n]++;
    }
  }
}
  
  function recursionPar(items, level){
    for(var _i = level; _i < items.length;_i++ ){
    var n = items[i]+','+items[_i];
    if( items[i] > items[_i] ){
      n = items[_i]+','+items[i];
    }
      
      return n;
  }
  
for(var t = 0; t < stat.length; t++){
findDouble(stat[t]);
}

*/


