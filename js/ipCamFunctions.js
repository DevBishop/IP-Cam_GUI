
var camWebApi = {
	
	 webApiUrl : function(){
		 return "http://192.168.1.238:88/cgi-bin/CGIProxy.fcgi?";
	 },
     
	 //0:admin, 1:operator, 2:viewer
	 userCredentialsByType : function(typeOfUser){
		 switch(typeOfUser){
			case 1:
				return {user : "matteoo", password : "matteoo"}; //operator
			case 2:
				return {user : "matteov", password : "matteov"}; //viewer
			default:
				return {};
		 }
	 },
     
	 //https://css-tricks.com/snippets/javascript/javascript-keycodes/
	 requiredActionFromKeyCode : function(e){
         debugger;
			switch(e.keyCode) {
			case 37:
				return "Left"
			case 38: 
				return "Up"
			case 39: 
				return "Right"
			case 40: 
				return "Down"
			default: 
            return; 
		 }
	 },
	 
	 buildWebApiGetFromGenericRequest : function(action, typeOfUser){
		 var url = "";
		 switch(action){
			 case "snapPicture2":
			 	url = this.webApiUrl() + "cmd=" + 
			 	action + "&usr=" + 
				this.userCredentialsByType(typeOfUser).user + "&pwd=" +
		 		this.userCredentialsByType(typeOfUser).password + "&"; 
				return url;
		 }
	 },
	 
	 buildWebApiGetFromKeyCodeAction : function(action, e){
		var url = "";
		 switch (action) {
			 case "Move":
			 	url = this.webApiUrl() + "cmd=ptzMove" + 
				this.requiredActionFromKeyCode(e) + "&usr=" + 
				this.userCredentialsByType(1).user + "&pwd=" +
		 		this.userCredentialsByType(1).password; 
				$.getJSON(url, function(data){});
				 break;
			 case "Stop":
				url = this.webApiUrl() + "cmd=ptzStopRun" + 
				this.requiredActionFromKeyCode(e) + "&usr=" + 
				this.userCredentialsByType(1).user + "&pwd=" +
		 		this.userCredentialsByType(1).password;
				$.getJSON(url, function(data){});
				break;
		 }
	 },
     buildWebApiStopRunUrl : function(){
                this.webApiUrl() + "cmd=ptzStopRun" + 
				this.requiredActionFromKeyCode(e) + "&usr=" + 
				this.userCredentialsByType(1).user + "&pwd=" +
		 		this.userCredentialsByType(1).password;
          
				$.getJSON(url, function(data){});
     },
     
     xmlParser : function(xmlText){
         if (window.DOMParser)
            {
                parser=new DOMParser();
                xmlDoc=parser.parseFromString(xmlText,"text/xml");
            }
            else // Internet Explorer
            {
                xmlDoc=new ActiveXObject("Microsoft.XMLDOM");
                xmlDoc.async=false;
                xmlDoc.loadXML(txt);
            }
            
            var xmlObj = {
          
                user0 = xmlDoc.getElementsByTagName("user0")[0].childNodes[0].nodeValue,
                password0 = xmlDoc.getElementsByTagName("password0")[0].childNodes[0].nodeValue,
            }
     }
	 
	 
	
}