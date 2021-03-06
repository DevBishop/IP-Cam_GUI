
var camWebApi = {
    
    webApiUrl : function(hasLocalIP){
        var linkConstants = '/cgi-bin/CGIProxy.fcgi?';
        if(hasLocalIP){
            return this.xmlParser('ipCamUrl', 0).url + linkConstants;
        }
        else{
            return this.xmlParser('ipCamExternalUrl', 0).url + linkConstants;
        }
        
    },
    
    xmlFilePath : function(){
        return '/../config.xml';
    },
    
    userCredentialsByType : function(typeOfUser){
        return this.xmlParser('credentials', typeOfUser);
    },
	
	ipCameraModelName : function(){
		return this.xmlParser('ipCamParams',0).ipCamModelName;	
	},
    
    requiredActionFromKeyCode : function(e){
            switch(e.keyCode) {
            case 37:
                return "ptzMoveLeft"
            case 38: 
                return "ptzMoveUp"
            case 39: 
                return "ptzMoveRight"
            case 40: 
                return "ptzMoveDown"
            case 32:
                return "ptzStopRun"    
            default: 
            return; 
        }
    },
    
    buildWebApiGetFromGenericRequest : function(action, typeOfUser, hasLocalIP){
        var url = "";
        switch(action){
            case "snapPicture2":
                url = this.webApiUrl(hasLocalIP) + "cmd=" + 
                action + "&usr=" + 
                this.userCredentialsByType(typeOfUser).user + "&pwd=" +
                this.userCredentialsByType(typeOfUser).password + "&"; 
                return url;
        }
    },
    
    buildWebApiGetFromKeyCodeAction : function(action,e){
        
        if($.inArray(e.keyCode, [37, 38, 39, 40, 32]) === -1){
            return;
        }
		if(this.ipCameraModelName() !== 'FI9828W'){
			$.prompt('Questo modello di ipCam non &egrave; motorizzato')
			return;
		}
        
        var url = "";
        switch (action) {
            case "Move":
                url = this.webApiUrl() + "cmd=" + 
                this.requiredActionFromKeyCode(e) + "&usr=" + 
                this.userCredentialsByType(1).user + "&pwd=" +
                this.userCredentialsByType(1).password; 
                $.get(url);
                break;
        }
    },
    
    xmlFileGetter : function(xmlFile){
        var xmlDoc;
        if(typeof window.DOMParser != "undefined") {
            xmlhttp=new XMLHttpRequest();
            xmlhttp.open("GET",xmlFile,false);
            if (xmlhttp.overrideMimeType){
                xmlhttp.overrideMimeType('text/xml');
            }
            xmlhttp.send();
            xmlDoc=xmlhttp.responseXML;
        }
        else{
            xmlDoc = new ActiveXObject("Microsoft.XMLDOM");
            xmlDoc.async="false";
            xmlDoc.load(xmlFile);
        }
        return xmlDoc;
    },
    
    xmlParser : function(typeOfNode, typeOfUser){
        var xmlDoc = this.xmlFileGetter(this.xmlFilePath());
        var pNode=xmlDoc.getElementsByTagName(typeOfNode);
        
        switch(typeOfNode){
            case "credentials":
                return credentialsObj = {
                    user : pNode[0].getElementsByTagName("user" + typeOfUser)[0].childNodes[0].nodeValue,
                    password : pNode[0].getElementsByTagName("password" + typeOfUser)[0].childNodes[0].nodeValue,
                };
            case "ipCamExternalUrl":
            case "ipCamUrl":
                return urlObj = {
                    url : pNode[0].getElementsByTagName("url")[0].childNodes[0].nodeValue
                };
			case "ipCamParams":
				return obj = {
					ipCamModelName : pNode[0].getElementsByTagName("ipCamModelName")[0].childNodes[0].nodeValue
				};
            default:
                return "";
        }   
    }
    
}