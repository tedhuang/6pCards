
//Helper function to extract GET values from URL
function getUrlVars() {
	var vars = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi,
			function(m, key, value) {
				vars[key] = value;
			});
	return vars;
}


//Helper functions to get and set cookie
//credit: http://www.w3schools.com/
function setCookie(c_name,value,exdays)
{
	var exdate=new Date();
	exdate.setDate(exdate.getDate() + exdays);
	var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString());
	document.cookie=c_name + "=" + c_value;
}
function getCookie(c_name)
{
var i,x,y,ARRcookies=document.cookie.split(";");
for (i=0;i<ARRcookies.length;i++)
{
  x=ARRcookies[i].substr(0,ARRcookies[i].indexOf("="));
  y=ARRcookies[i].substr(ARRcookies[i].indexOf("=")+1);
  x=x.replace(/^\s+|\s+$/g,"");
  if (x==c_name)
    {
    return unescape(y);
    }
  }
}

function makeAPICall(type, module, command, data, callback) {
    var urlParts = [];
    for (var param in data) {
      urlParts.push(param + "=" + data[param]);
    }
    url = URL_BASE + API_URL_PREFIX + '/' + module + '/' + command + '?' + urlParts.join('&');
    var handleError = function(errorObj) {}

    var httpRequest = new XMLHttpRequest();
    httpRequest.open("GET", url, true);
    httpRequest.onreadystatechange = function() {
      if (httpRequest.readyState == 4 && httpRequest.status == 200) {
        var obj;
        if (window.JSON) {
        	obj = JSON.parse(httpRequest.responseText);
            // TODO: catch SyntaxError
        } else {
            obj = eval('(' + httpRequest.responseText + ')');
        }
        if (obj !== undefined) {
          if ("response" in obj) {
            callback(obj["response"]);
          }

          if ("error" in obj && obj["error"] !== null) {
            handleError(obj["error"]);
          } else {
            handleError("response not found");
          }
        } else {
          handleError("failed to parse response");
        }
      }
    }
    httpRequest.send(null);
  }
