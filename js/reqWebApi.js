
var asyncWrapper = {
    
    ajaxWrappper : function(data, url){
        debugger;
        var resObj = {};
        $.ajax({
            url: url,
            type: "POST",
            data: data,
            success: function(res) {
                if(res){
                            debugger;

                    resObj = {
                        value : res,
                        error : ""
                    }
                }
                return resObj;
            },
            error: function(err){
                resObj = {
                        value : "",
                        error : err
                    } 
                    return resObj;
            } 
        });
    },
    ajaxWrap : function(data) {
        return this.ajaxWrappper(data, 'ajax_handler.php')
    }
}