$.preloadImages = function() {
       for(var i = 0; i<arguments.length; i++) {
               $("<img />").attr("src", arguments[i]);
       }
}

function GET_var(varurl, varname) {
    varvalue = varurl.match("\\?"+varname+"=(\\w+)");
    
    if (varvalue != null)
    {
        return varvalue[1];
    } 
    else
    {
        return null;
    }
}

$(document).ready(function() {
       $.preloadImages("");
});

jQuery(".answers .category-content").stop().slideUp(0);
jQuery(".category-answers-content p").stop().slideUp(0);

jQuery(document).ready(function() {
    var location = window.location.href; 
    var ticket_id = GET_var(location, "ticket_id");
    var currentUser = {};      

    currentUser.ticket_id = ticket_id;

    if(window.location.search == "?ticket_id=" + ticket_id || window.location.search == "?action=create_ticket") 
    {
        var socket = io.connect('http://localhost:22333');

        socket.on("connect", function (server) {

            socket.emit("new_connection", key);

            socket.on("status", function(data) {

                var parsedData = JSON.parse(data);

                if(parsedData == null)
                    return;

                /*if(parsedData.status == "user_connected")
                {
                    console.log("New user with id = " + parsedData.data.user_id + " was connected!");
                }*/

                /*if(parsedData.status == "joined")
                    console.log("You was joined to = " + parsedData.to);*/
            });
            
            socket.on("recived_message_from_support", function(msg) {
                if($(".box-message").is(":visible"))
                    $(msg).insertAfter(".box-message:last-child");
                else
                    $(msg).insertAfter(".box-message");
            });

            socket.on("del_message", function(id) {
                $(".message-id-" + id).remove();
            });

            socket.on("user_typing", function(id) {
                $(".typing").text("Печатает...");

                window.setTimeout(function () {
                    $(".typing").text("");
                }, 2500)
            });
                
        });
    }

    $("#comment").keydown(function() {
        socket.emit("typing");
    });

    $("#page-loading").hide();
    
     $(".close-ticket, .no").click(function(){
            $("#dialog").slideUp();
            return false;
        });
        
        $("#submit_ticket_close_link").click(function(){
            $("#dialog").slideDown();
            return false;
        });

    jQuery(".toggle-nav").click(function() {
        $("#navigation_header").stop().toggle(0);

        return false;
    });

    $(window).resize(function(){
        var wid = $(window).width();
        if(wid > 865 && $("#navigation_header").is(':hidden')) 
        {
            $("#navigation_header").removeAttr('style');
        }
    });

	jQuery('#navigation_header li a').each(function() {   
            var search = GET_var(location, "search")
            var link = this.href;  

            var reg = new RegExp("\d");

            if(location == "http://" + document.domain + "/?ticket_id=" + ticket_id)
            {
            	$(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?close_ticket=" + ticket_id)
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?close_ticket=")
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?search=" + search)
            {
                $(".answer-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?action=create_ticket")
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?create_ticket")
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?create_ticket=true")
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?action=login")
            {
                $(".tickets-menu").addClass('active_menu');
            }

            if(location == "http://" + document.domain + "/?action=logout")
            {
                $(".tickets-menu").addClass('active_menu');
            }
            
            if(location == link) 
            {      
                $(this).addClass('active_menu');  
            }
        });

    jQuery(".category-head").click(function() {
        jQuery(this).next().stop().slideToggle();
    });

    jQuery(".category-answers li").click(function() {
        jQuery(this).next().find("p").stop().slideToggle();
    });

    /**
    * Сообщениe
    */

    jQuery("#submit_message").click(function() {
        var inProgress = false;
        var message = jQuery("#comment").val();
        var url = window.location.href;
        var user = jQuery("#user-name-in-ticket").val();

        var ticket_id = GET_var(url, "ticket_id");
       
        if(!inProgress)
        {
            jQuery.ajax({
                url: "/core/add-user-message.php",
                method: 'POST',
                data: {"message" : message, "ticket_id" : ticket_id, "user" : user, "submit" : "message"},

                beforeSend : function() {
                    inProgress = true;
                },

            }).done(function(data) {
                inProgress = false;
                var content = jQuery.parseJSON(data);
                jQuery("#comment").val("");
                if(content.status == "ok")
                {   
                    if($(".box-message").is(":visible"))
                        $('<div id="user-message" class="box-message"><img alt="" src="' + content.avatar+'" class="user_image" ><div class="info-chat"><span class="user-name chat">'+ content.user +'</span><span class="date_message">'+content.time+'</span></div><p class="message">'+content.message+'</p></div>').insertAfter(".box-message:last-child");
                    else
                        $('<div id="user-message" class="box-message"><img alt="" src="' + content.avatar+'" class="user_image" ><div class="info-chat"><span class="user-name chat">'+ content.user +'</span><span class="date_message">'+content.time+'</span></div><p class="message">'+content.message+'</p></div>').insertAfter(".box-message");
                    
                    socket.emit("new_message_from_user", content.to_support);
                }
                else
                    if(content.error)
                    {
                        if($(".box-message").is(":visible"))
                            $('<div id="user-message" class="box-message"><div class="info-chat"><span class="user-name chat"></span><span class="date_message"></span></div><p style="text-align:center;color:#C93756;"class="message">'+content.error+'</p></div>').insertAfter(".box-message:last-child");
                        else
                            $('<div id="user-message" class="box-message"><div class="info-chat"><span class="user-name chat"></span><span class="date_message"></span></div><p style="text-align:center;color:#C93756;"class="message">'+content.error+'</p></div>').insertAfter(".box-message");
                    }

            });
        }

        return false;
    });

    jQuery(".yes").click(function() {

        var ticket = GET_var(window.location.search, "ticket_id");
        socket.emit("close_ticket", ticket);

    });

    jQuery("#submit_ticket").click(function() {

        var inProgress = false;  

        var message = jQuery("#user_message_ticket").val();
        var name = jQuery("#ticket_name").val();
        var category = jQuery("#category").val();
        var user = jQuery("#user-name-in-ticket").val();
        //var recaptcha = jQuery("#g-recaptcha-response").val();

        if(!inProgress)
        {
            jQuery.ajax({
                url: "/core/create-ticket.php",
                method: 'POST',
                data: {"user_message" : message, "name" : name, "category" : category, "submit" : "ticket", "user" : user},

                beforeSend : function() {
                    inProgress = true;
                },

            }).done(function(data) {
                inProgress = false;

                var content = jQuery.parseJSON(data);

                if(content.info)
                {
                    jQuery("#ticket_create_wrapper .information-box").remove();
                    jQuery("#ticket_create_wrapper .error-box").remove();
                    jQuery('<div class="information-box"><p>' + content.info + '</p></div>').insertBefore("#ticket_create_wrapper form");
                    
                    socket.emit("new_ticket", content.ticket_id, content.to);

                    setTimeout(document.location.href="/?ticket_id=" + content.ticket_id, 5000);
                }
                if(content.error)
                {

                    jQuery("#" + content.id).addClass("error-data");

                    jQuery("#ticket_create_wrapper .error-box").remove();
                    jQuery('<div class="error-box"><p>' + content.error + '</p></div>').insertBefore("#ticket_create_wrapper form");
                }
            });
        }

        return false;
    });

    jQuery("#submit_ticket_close").click(function() {

        var inProgress = false;  

        var ticket_id = jQuery("#ticket_close_id").val();
       
        if(!inProgress)
        {
            jQuery.ajax({
                url: "?action=close_ticket",
                method: 'POST',
                data: {"ticket_id" : ticket_id},

                beforeSend : function() {
                    inProgress = true;
                },

            }).done(function(data) {
                inProgress = false;

                var content = jQuery.parseJSON(data);

                if(content.info)
                {
                    jQuery("#ticket_create_wrapper .information-box").remove();
                    jQuery("#ticket_create_wrapper .error-box").remove();
                    jQuery('<div class="information-box"><p>' + content.info + '</p></div>').insertBefore("#ticket_create_wrapper form");
                }
                if(content.error)
                {

                    jQuery("#" + content.id).addClass("error-data");

                    jQuery("#ticket_create_wrapper .error-box").remove();
                    jQuery('<div class="error-box"><p>' + content.error + '</p></div>').insertBefore("#ticket_create_wrapper form");
                }
            });
        }

        return false;
    });

    $('input').iCheck({
        /*checkboxClass: 'icheckbox_flat-green',
        radioClass: 'iradio_flat-green'*/
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square-green',
        increaseArea: '20%' // optional
    });
});

