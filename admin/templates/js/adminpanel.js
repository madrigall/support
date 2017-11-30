jQuery(function() {
    var host = window.location.host;
    var ScreenWidth = window.innerWidth;     
    var ScreenHeight = window.innerHeight; 
    var socket;
    var arr_online = new Array();
       
    //$(".set-authorization").slideUp(0);
   
    $("#u-add-support-v, #support_edit_form").validationEngine();

    $("#ticket_content").hide();

    $('input').iCheck({
        checkboxClass: 'icheckbox_square-green',
        radioClass: 'iradio_square_green',
        increaseArea: '20%' // optional
    });

    $('.checkbox-users,.select-all-users,.add-answer').iCheck({
        checkboxClass: 'icheckbox_square-blue',
        radioClass: 'iradio_square_blue',
        increaseArea: '20%' // optional
    });

    $(".select-all-users.head").on("ifClicked", function(e){

        $(this).on("ifChecked", function(){
            $(".checkbox-users").iCheck("check");
            $(".select-all-users.foot").iCheck("check");
        });

        $(this).on("ifUnchecked", function(){
                $(".checkbox-users").iCheck("uncheck");
                $(".select-all-users.foot").iCheck("uncheck");
        });
        
    });

    $(".select-all-users.foot").on("ifClicked", function(e){

        $(this).on("ifChecked", function(){
            $(".checkbox-users").iCheck("check");
            $(".select-all-users.head").iCheck("check");
        });

        $(this).on("ifUnchecked", function(){
                $(".checkbox-users").iCheck("uncheck");
                $(".select-all-users.head").iCheck("uncheck");
        });
        
    });

    $(".checkbox-users").each(function(){
        $(this).on("ifChecked", function(){
            $(".u-action-panel").show();
        });

        $(this).on("ifUnchecked", function() {
            $('.u-action-panel').hide()
        });
    
    });

    $(".menu-mobile").click(function() {
        
        var menu = $('.cbp-vimenu').css('left');
        $(".cbp-vimenu li a span").remove();

        var li_w = parseInt($('.cbp-vimenu li').css('width'));
        var menu_w = parseInt($(".menu-mobile").css('width'));

        var padding = (li_w - menu_w) / 2;

        if(menu === "-70px")
        {
            $(".cbp-vimenu").css("left", "0px");
            $(".notification").css("right", "-10px");

            $(".menu-mobile").css("padding-left", -padding);

            $("#info_in_head").css("display", "table-cell");

        }
        else
        {
            $(".cbp-vimenu").css("left", "-70px");
            $(".menu-mobile").css("padding-left", "0");
            $("#info_in_head").css("display", "none");
        }
    }); 

    tinymce.init({
            selector: "textarea#form-message-text, #support-answer textarea",
            plugins: [
                "advlist autolink lists link image charmap print preview anchor",
                "searchreplace visualblocks code fullscreen",
                "insertdatetime media table contextmenu paste save",
            ],
            toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image",
            skin: "light",
            content_css : "../core/tinymce/js/tinymce/skins/light/style.css",
            theme_advanced_resizing: true,
            setup: function(editor) {
                    editor.on('keypress', function(e) {
                        socket.emit("typing");
                    });
                }
        });

    $(window).resize(function(){
        var wid = $(window).width();
        //865
        if(wid > 900 && $(".main-navigation").is(':hidden')) 
        {
            $(".main-navigation").removeAttr('style');
        }
    });

	jQuery(document).ready(function() {
        //Sockets
        var intervalID;
        var ticket = GET_var(window.location.search, "ticket_id");
        socket = io.connect('http://localhost:22333');

        socket.on("connect", function (server) {
            /**
             * Connecting
             */
            socket.emit("new_connection", key);
           
            /**
             * Pushed to room
             */
            if(window.location.search == "?ticket_id=" + ticket) 
            {
               socket.emit("join_to_room", ticket);
            }

            /**
             * New message
             */
            socket.on("recived_message_from_user", function(msg) {
                $(msg).insertAfter(".message-wrapper:last-child").addClass("animated slideInRight");
            });

            socket.on("recived_message_n", function(status) {
                if(status)
                    $.jGrowl("У вас новое сообщение!");
            });

            /**
             * End
             */
            
            /**
             * Ticket is Closed
             */
            socket.on("close_ticket_to_support", function(id) {
                if(window.location.search == "?ticket_id=" + id)
                {
                    modal("ticket-closed", "Пользователь закрыл данный тикет!");
                    $(".wrapper-form").remove();
                }
            });

            /**
             * Typing
             */
            socket.on("user_typing", function(id) {
                $(".typing").text("Печатает...");

                window.setTimeout(function () {
                     $(".typing").text("");
                }, 2500)
            });

            /**
             * Online
             */
            socket.on("online", function(data) {
                arr_online.sup = (data[0]);
                arr_online.user = (data[1]);

                if(window.location.search == "?ticket_id=" + ticket) 
                {
                    arr_online["sup"].forEach(function(element, index){
                        $(".support-" + element).addClass("support-online");
                    });

                    arr_online["user"].forEach(function(element, index){
                        $(".user-" + element).addClass("user-online");
                    });
                    
                    doBlink(2000,.4);
                    intervalID = setInterval(function(){doBlink(950,.6);}, 1500);
                

                    $(".user-online").each(function() {
                        var value = ($(this).data("value"));

                        if($.inArray(value, arr_online["user"]) == "-1")
                        {
                            $(".user-" + value).removeClass("user-online");
                        }

                    });

                    $(".support-online").each(function() {
                        var value = ($(this).data("value"));

                        if($.inArray(value, arr_online["user"]) == "-1")
                        {
                            $(".user-" + value).removeClass("user-online");
                        }

                    });
                }
            });

            /**
             * New ticket
             */
            socket.on("new_ticket_s", function(id, to) {
                var form_data = {"ticket_id": id, "support_id" : to ,"submit_message" : true};

                ajaxJSON("modules/update-tickets.php", "POST", form_data).done(function(data) {
                    var content = $.parseJSON(data);
                    if(content.ticket)
                    {
                        $.jGrowl("У вас новый тикет!");

                        if($('*').is('.statement-container .row')) {
                            $(".statement-container .row").prepend(content.ticket);
                        }
                        else {
                            $("<div class='row'>").insertAfter($("#main-container .information-message"))
                            $("#main-container .row").append(content.ticket);
                        }
                    }
                });

            });

            /**
             * User status
             */
            
            setInterval(send_request, 20000);


        });
        
        function send_request()
        {
            socket.emit("give_me_users", true);
        }

        function doBlink(fullTime,minVal){
            $(".user-online").fadeTo(fullTime/2,minVal,function(){
                $(".user-online").fadeTo(fullTime/2,1);
            });

            $(".support-online").fadeTo(fullTime/2,minVal,function(){
                $(".support-online").fadeTo(fullTime/2,1);
            });
        }

        $("#page-loading").hide();
        
       /* $("#authorization-slide").click(function(){
            $(".set-authorization").stop().slideToggle(0);
            $(".row-table >").toggleClass("fa-arrow-down");
        });*/

        $('#settings-tabs li a').click(function (e) {
            e.preventDefault();
            $(this).tab('show');
        })
        $(window).scroll(function() 
        {
            if(ScreenWidth >= 600)
            {
                if($(window).scrollTop() >= $("header").height()) 
                {
                    $(".cbp-vimenu").stop().animate({"margin-top": "0"}, 100);
                    $(".cbp-vimenu li a.icon-logo").css({"display": "block"}, 100);
                }
                else
                {
                    $(".cbp-vimenu li a.icon-logo").css({"display": "none"}, 100);
                    $(".cbp-vimenu").stop().animate({"margin-top": "70px"}, 100);
                }
            }
            else
            {
                if($(window).scrollTop() >= $("header").height()) 
                {
                    $(".cbp-vimenu").stop().animate({"margin-top": "0"}, 0);
                    $(".cbp-vimenu li a.icon-logo").css({"display": "block"}, 0);
                }
                else
                {
                    $(".cbp-vimenu li a.icon-logo").css({"display": "none"}, 0);
                    $(".cbp-vimenu").stop().animate({"margin-top": "70px"}, 0);
                }
            }
        });
        
        /**
         * Выделяем пункт меню текущей страницы
         */

        $('.main-navigation .cbp-vimenu li a').each(function() {   

            var location = window.location.href; 
            var link = this.href;  
            var ticket = GET_var(window.location.search, "ticket_id");

            if(window.location.search === "?ticket_id=" + ticket) 
            {
                $("a[href='?mode=tickets']").parent().addClass('cbp-vicurrent');  
            }
            if(location === link) 
            {      
                $(this).parent().addClass('cbp-vicurrent');  
            }

        });//each

        /**
         * Add Support Message
         */

        $("#support-message-add").click(function() {

            var ticket_id = $("#ticket-id").val();
            var support_id = $("#support-id").val();
            var message = tinyMCE.activeEditor.getContent();
            
            var form_data = {"ticket_id" : ticket_id, "message" : message, "submit_message" : true, "support_id" : support_id};
            
            ajaxJSON("modules/add-support-message.php", "POST", form_data).done(function(data) {
                var content = $.parseJSON(data);

                if(content.error)
                {
                    modal("message-error", content.error);
                }
                else
                    if(content.content)
                    {
                        $("#page-loading").css("background" , "none");
                        $("#page-loading").show();
                        
                        $(content.content).insertAfter(".message-wrapper:last-child").addClass("animated slideInRight");
                        socket.emit("new_message_from_support", content.to_user);
                       
                        tinyMCE.activeEditor.setContent("");

                        $("#page-loading").delay(100).fadeOut(0);
                    }
                
            });

            return false;
            
        });//support-message-add

        /**
         * Add a tag
         */   

        $("#add-category").click(function() {
            var category = $("#category-name").val();

            var form_data = {"add-category": "true", "category" : category};
            
            ajaxJSON("modules/add-category.php", "POST", form_data).done(function(data) {
                var content = $.parseJSON(data);

                if(content.error)
                {
                    modal("message-error", content.error);
                }
                else
                    if(content.done)
                    {
                        $(".categories").append($('<span class="tag"><span class="tag-content">' + category + '</span><a href="#" class="delete-category"><i class="fa fa-times my-times"></a></i></span>')).addClass("jello");
                        $('<span class="tag answers-tag"><div class="icheckbox_square-blue" style="position: relative;"><input class="add-answer" type="checkbox" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"><ins class="iCheck-helper" style="position: absolute; top: -20%; left: -20%; display: block; width: 140%; height: 140%; margin: 0px; padding: 0px; border: 0px; opacity: 0; background: rgb(255, 255, 255);"></ins></div><span class="tag-content answers-content">' + category + '</span></span>').insertAfter((".last"));
                        $('#sup-answ').append($('<div class="support-answers"><div class="support-cat-name">' + category + '</div><p class="description margin-top-bottom">Ответов нет.</p></div>')).addClass("fadeInRight");
                        $("#category-name").val("");
                    }
                
            });

            return false;
            
        });//support-message-add

        $(".delete-category").click(function() {
            var category = $(this).parent().text();
            var id = $(this).parent().attr("id");

            var form_data = {"delete-category": "true", "category" : category};
            
            ajaxJSON("modules/delete-category.php", "POST", form_data).done(function(data) {
                var content = $.parseJSON(data);

                if(content.error)
                {
                    modal("message-error", content.error);
                }
                else
                    if(content.done)
                    {
                        $("span#" + id + ".tag.answers-tag").remove();
                        $("div#" + id + ".support-answers").remove();
                        $("#" + id).remove();
                    }
            });

            return false;
            
        });

        $(".delete-answer-b").click(function() {
            var id = $(this).attr("id");

            var form_data = {"delete-answer": "true", "id" : id};
            
            ajaxJSON("modules/delete-answer.php", "POST", form_data).done(function(data) {
                var content = $.parseJSON(data);

                if(content.error)
                {
                    modal("message-error", content.error);
                }
                else
                    if(content.done)
                    {
                        $("#" + id + ".answers").remove();
                    }
                
            });

            return false;
            
        });

        /**
         * Draggable
         */

        if(ScreenWidth >= 600)
        {
            $(".open.message-wrapper#support-message").draggable({
                    
                    revert: true,
                    zIndex: 1,
                    start: function(e) {
                         $(this).addClass("draggable");
     
                        },
                    stop: function(){
                        $(this).removeClass("draggable");
                    }
            });

            $(".error-info .modal-action").click(function(){
                window.location.href = "/admin";
            });

            $("#delete-area").droppable({
                activeClass: "delete-show",
                hoverClass: "hover-delete-area",
                drop: function(){
                    var drag = $(".draggable");
                    var message_id = $(drag).find(".message").attr("id");
                    var written_by = $(drag).attr("id");
                    
                    //id = delete-message
                    dialog("delete-message", "Удаление", "Вы действительно хотите удалить сообщени?");

                    $(".dialog-button").click(function(e) {
                        if(e.target.id === "dialog-yes-delete-message")
                        {
                            var dialog_on_page = $(this);
                            var message_data = {"delete_message" : true, "message_id" : message_id, "written_by" : written_by};
                            ajaxJSON("modules/delete-message.php", "POST", message_data).done(function(data_message) {
                                console.log(data_message);
                                var content_message = $.parseJSON(data_message);
                                

                                if(content_message.done)
                                {
                                    $(drag).remove();
                                    socket.emit("delete_message", message_id);
                                    $("#delete-message").remove();
                                    modal("message-delete-info", content_message.done);
                                }
                                else
                                    if(content_message.error) 
                                    {
                                        $(dialog_on_page).parent().parent().remove();
                                        modal("message-delete-error", content_message.error);
                                    }
                            });
                            
                        }

                        if(e.target.id === "dialog-no-delete-message")
                            $("#delete-message").remove();
                    });
                }
            }); 
        }
    });//document.ready()
});
