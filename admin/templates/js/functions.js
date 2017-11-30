function GET_var(varurl, varname) {
    var value = varurl.match("\\?"+varname+"=(\\w+)");
    
    if (value != null)
    {
        return value[1];
    } 
    else
    {
        return null;
    }
}

function modal(modalId, modalText, img ) {

    this.overlay = "<div id='" + modalId + "'class='modal-overlay'></div>";
    if(!img)
        this.content = "<div class='modal-content'><button class='modal-action' data-modal-close='true'><i id='modal-button' class='fa fa-times fa-lg'></i></button><p>" + modalText + "</p></div>";
    else
        this.content = "<div class='modal-content'><button class='modal-action' data-modal-close='true'><i id='modal-button' class='fa fa-times fa-lg'></i></button><img style='max-width:100%;' src='"+ img +"' alt=''></div>";
    
    $(this.overlay).insertBefore("header");
    $(this.content).appendTo("#" + modalId).addClass('animated bounce');

    $("#"+modalId).delay(1000).fadeOut(200, function(){ $(this).remove();})
    
};

function dialog(dialogId, dialogTitle, dialogText) {
	this.overlay = "<div id='" + dialogId + "'class='dialog-overlay'></div>";

    this.content = "<div class='dialog-content'><p class='dialog-title'>"+dialogTitle +"<hr></p><br><p>" + dialogText + "</p><br><button id='dialog-yes-" + dialogId + "' class='dialog-button'><i id='dialog-yes-" + dialogId + "' class='fa fa-check'></i></button><button id='dialog-no-" + dialogId + "' class='dialog-button'><i id='dialog-no-" + dialogId + "' class='fa fa-times'></i></button></div>";

    $(this.overlay).insertBefore("header");
    $(this.content).appendTo("#" + dialogId).addClass('animated bounce');

}

function ajaxJSON(url, method, data) {	
	var inProgress = false;

	if(!inProgress)
		var answerData = $.ajax({
			url: url,
			method: method,
			data: data,

			beforeSend : function() {
			    inProgress = true;
			}

		})
		.done(function(responseData, status, xhr) {
			inProgress = false;
		})
		.fail(function (xhr, status, err) {
			modal("message-error", "Ошибка при отправке запроса!");
			inProgress = false;
		});

	return answerData;
}
