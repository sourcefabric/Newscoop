var statusMap ={ 'pending': 'status_new', 'hidden': 'status_hidden', 'deleted': 'status_deleted', 'approved': 'status_approved' };
var datatableCallback = {
	serverData: {},
	row: function(nRow, aData, iDisplayIndex, iDisplayIndexFull) {
	    that = datatableCallback;
        var tr = $('<tr></tr>');
        tr.hover(function() {
        	   $(this).find(".commentBtns").css("visibility","visible");
            },function() {
            	$(this).find(".commentBtns").css("visibility","hidden");
        });/*
        .click(function(e){
            //$(this).find(".action-edit").click();
            e.preventDefault();
        	return false;
        });
        */
        var td = $('<td></td>');
        td.attr('colspan',5);
        nRow = $(nRow);
        tr.addClass(statusMap[nRow.find('.status-code').val()]);
        actions = nRow.find('.commentAction').first();
        nRow.find('.commentTimeCreated').removeClass('commentTimeCreated').addClass('commentMessage');
        nActions = actions.clone();
        actions.hide();
        td.append($('<table cellspacing="0" cellpadding="0">').append(nRow));
        td.append(nActions.html());
        jRow = tr.append(td);
        that.rowIndex++;
        return jRow.get(0);
    },
    addServerData: function ( sSource, aoData, fnCallback ) {
    	that = datatableCallback;
        for(i in that.serverData) {
            if(that.serverData[i])
                aoData.push({"name": "sFilter[status][]", "value": i});
        }
    	$.getJSON( sSource, aoData, function (json) {
    		fnCallback(json);
    	});
    }
};
$(function(){
    // Tabs
    $('.tabs').tabs();
    $('.tabs').tabs('select', '#tabs-1');
    var commentFilterTriggerCount = 0;
    $("#commentFilterTrigger").click(function () {
        if (commentFilterTriggerCount == 0){
            $("#commentFilterSearch").css("display","block");
            $(this).addClass("collapsed");
            commentFilterTriggerCount = 1;
        } else {
            $("#commentFilterSearch").css("display","none");
            $(this).removeClass("collapsed");
            commentFilterTriggerCount = 0;
        }
    });

    $(".commentsHolder table tbody tr").hover(function() {
        $(this).find(".commentBtns").css("visibility","visible");
    },function() {
        $(this).find(".commentBtns").css("visibility","hidden");
           });

    $(".addFilterBtn").click(function () {$('#commentFilterSearch fieldset ul').append('<li><select class="input_select"><option>1</option><option>2</option></select><input type="text" class="input_text" /></li>'); return false; $("#commentFilterSearch").css("height","500px");});

    /**
     * Action to fire
     * when header filter buttons are triggresd
     */
    $('.status_filter li').click(function(evt){
        var ck = $(this).find('input[type="checkbox"]');
        var checked = true;
        if (ck.attr("checked")) {
            checked = false;
            ck.removeAttr("checked");
        }
        else
            ck.attr("checked","checked");
        datatableCallback.serverData[ck.val()] = checked;
        datatable.fnDraw();
        evt.preventDefault();
    });
    
    /**
     * Action to fire
     * when action select is triggered
     */
    $('.actions').change(function(){
        action = $(this);
        var status = action.val();
        if(status != '')
        {
            ids = [];
            $('.table-checkbox:checked').each(function(){
                ids[ids.length] = $(this).val();
            });
            action.val('');
            if(!ids.length)
                return;
            $.ajax({
                type: 'POST',
                url: 'comment/set-status/format/json',
                data: $.extend({
                   "comment": ids,
                   "status": status                   
                },serverObj.security),
                success: function(data) {
                    flashMessage(putGS('Comments updated.'));
                	datatable.fnDraw();
                },
                error: function (rq, status, error) {
                    if (status == 0 || status == -1) {
                        flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                    }
                }
            });
        }
    });
    $('.sort_tread').click(function(){
        var dir = $(this).find('span');
        if(dir.hasClass('ui-icon-triangle-1-n'))
        {
            dir.removeClass("ui-icon-triangle-1-n");
            dir.addClass('ui-icon-triangle-1-s');
            datatable.fnSort( [ [2,'asc'] ] );
        }
        else {
            dir.removeClass("ui-icon-triangle-1-s");
            dir.addClass('ui-icon-triangle-1-n');
            datatable.fnSort( [ [2,'desc'] ] );
        }
        dir.removeClass("ui-icon-carat-2-n-s");
    });
	$('.datatable .action').live('click',function(){
	    var el = $(this);
	    var id = el.attr('id');
	    var ids = [id.match(/\d+/)[0]];
	    var status = id.match(/[^_]+/)[0];
        $.ajax({
            type: 'POST',
            url: 'comment/set-status/format/json',
            data: $.extend({
               "comment": ids,
               "status": status
            },serverObj.security),
            success: function(data) {
                datatable.fnDraw();
                flashMessage(putGS('Comments updated.'));
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });

	});
	$('.dateCommentHolderEdit form').live('submit', function(){
	    var that = this;
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function(data) {
                datatable.fnDraw();
                flashMessage(putGS('Comment updated.'));
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
        return false;
	});
	$('.dateCommentHolderEdit .edit-cancel,.dateCommentHolderEdit .reply-cancel').live('click', function(){
	    var el = $(this);
	    var td = el.parents('td');
	    var form = el.parents('form');
	    $(form).each(function(){
            this.reset();
        });
	    td.find('.commentSubject,.commentBody').slideDown("fast");
	    td.find('.content-edit').slideUp("fast");
	    td.find('.content-reply').slideUp("fast");
	});
    $('.dateCommentHolderEdit .edit-reply').live('click', function(){
        var el = $(this);
        var td = el.parents('td');
        var form = td.find('form');
        $(form).each(function(){
            this.reset();
        });
        td.find('.content-edit').slideUp("fast");
        td.find('.content-reply').slideDown("fast");
    });

	$('.datatable .action-edit').live('click', function(){
		 var el = $(this);
		 var td = el.parents('td');
		 td.find('.commentSubject').slideToggle("fast");
		 td.find('.commentBody').slideToggle("fast");
		 td.find('.content-edit').slideToggle("fast");
	});
        // Dialog
        $('.dialogPopup').dialog({
            autoOpen: false,
            width: 600,
            height: 560,
            position:'right',
            buttons: {
                "Cancel": function() {
                    $(this).dialog("close");
                }
            }
        });
        // Dialog Link
        $('.articleLink').live('click',function(){
        var that = this;
        $.ajax({
                type: 'GET',
                url: $(this).attr('href'),
                success: function(data) {
                      var content = '<h3><a href="#">'+$(that).html()+'</a></h3>';
                      for(i in data)
                      {
                    	  content+='<h4>'+i+'</h4>';
                    	  content+='<p>'+data[i]+'</p>';
                      }
                      $('.dialogPopup').html(content);
                      $('.dialogPopup').dialog('open');
                },
                error: function (rq, status, error) {
                    if (status == 0 || status == -1) {
                        flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                    }
                }
            });
            return false;
        });
});