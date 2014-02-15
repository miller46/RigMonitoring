$(document).ready(function() {

	GetTotalCoins();
	GetCurrentCoins();
	GetBTCVal();
});

function GetTotalCoins() {
	$.ajax( {
		type: "POST",
		url: '/monitoring/apiwrapper.php?op=total',
		dataType: 'text',
		cache: false,
		success: function(response)
		{
		    $('#totalMined').addClass('success').text(response);
		},
		error: function(xhr, status, error) 
		{
			$('#totalMined').addClass('failure').text('');
		}
	});
}

function GetCurrentCoins() {
	$.ajax( {
		type: "POST",
		url: '/monitoring/apiwrapper.php?op=current',
		dataType: 'text',
		cache: false,
		success: function(response)
		{
		    $('#currentMined').addClass('success').text(response);
		},
		error: function(xhr, status, error) 
		{
			$('#currentMined').addClass('failure').text('');
		}
	});
}

function GetBTCVal(resp) {
	//if ()
	$.ajax( {
		type: "GET",
		url: '/monitoring/apiwrapper.php?op=btc',
		dataType: 'text',
		cache: false,
		success: function(response)
		{
		    $('#btcVal').addClass('success').text(response);
		},
		error: function(xhr, status, error) 
		{
			$('#btcVal').addClass('failure').text('');
		}
	});
	return false;
}
function GetDogeToBTC() {

}