var statusMap = {
    'pending': 'new',
    'hidden': 'hidden',
    'deleted': 'deleted',
    'approved': 'approved'
};
var recommendedMap = {
	'recommended': 'recommended',
	'unrecommended': 'unrecommended'
};
var datatableCallback = {
    serverData: {},
    loading: false,
    addServerData: function (sSource, aoData, fnCallback) {
        that = datatableCallback;
        for (i in that.serverData) {
			if (i == 'pending' || i == 'processed' || i == 'starred' || i == 'deleted' || i == 'approved' || i == 'hidden') {
				if (that.serverData[i]) {
					aoData.push({
						"name": "sFilter[status][]",
						"value": i
					});
				}
			}
			else {
				if (that.serverData[i]) {
					if (i == 'recommended') var value = 1;
					else var value = 0;
					
					aoData.push({
						"name": "sFilter[recommended][]",
						"value": value
					});
				}
			}
        }
        $.getJSON(sSource, aoData, function (json) {
            fnCallback(json);
        });
    },
    row: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
		if (aData.comment.recommended == 1) $(nRow).addClass('recommended');
		else $(nRow).addClass('unrecommended');
		
		$(nRow)
            .addClass('status_' + statusMap[aData.comment.status])
            .tmpl('#comment-tmpl', aData)
            .find("input."+ statusMap[aData.comment.status]).attr("checked","checked");
        return nRow;
    },
    draw: function () {
        $(".commentsHolder table tbody tr").hover(function () {
            $(this).find(".commentBtns").css("visibility", "visible");
        }, function () {
            $(this).find(".commentBtns").css("visibility", "hidden");
        });
        datatableCallback.loading = false;
    },
    init: function() {
        $('.dataTables_filter input').attr('placeholder',putGS('Search'));
        $('#actionExtender').html('<fieldset>\
                                <legend>' + putGS('Actions') + '</legend> \
                                <select class="input_select actions">\
                                    <option value="">' + putGS('Select status') + '</option>\
                                    <option value="pending">' + putGS('New') + '</option>\
                                    <option value="approved">' + putGS('Approved') + '</option>\
                                    <option value="hidden">' + putGS('Hidden') + '</option>\
                                    <option value="deleted">' + putGS('Deleted')+ '</option>\
                                </select>\
                              </fieldset>');
        $('.actions').change(function () {
            action = $(this);
            var status = action.val();
            if (status != '') {
                ids = [];
                $('.table-checkbox:checked').each(function () {
                    ids[ids.length] = $(this).val();
                });
                action.val('');
                if (!ids.length) return;
                
                
                if (status == 'deleted' && !confirm(putGS('You are about to permanently delete multiple comments.') + '\n' + putGS('Are you sure you want to do it?'))) {
                    return false;
                }
                
                $.ajax({
                    type: 'POST',
                    url: 'comment/set-status/format/json',
                    data: $.extend({
                        "comment": ids,
                        "status": status
                    }, serverObj.security),
                    success: function (data) {
                        flashMessage(putGS('Comments status change to $1.', statusMap[status]));
                        datatable.fnDraw(false);
                    },
                    error: function (rq, status, error) {
                        if (status == 0 || status == -1) {
                            flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                        }
                    }
                });
            }
        });
        
        $('.table-checkbox').click(function(){
			if(!$(this).is(':checked')) {
				$('.toggle-checkbox').removeAttr('checked');
			}
		});
    }
};
$(function () {
	
	
    //$('.tabs').tabs();
    //$('.tabs').tabs('select', '#tabs-1');    
    var commentFilterTriggerCount = 0;
    $("#commentFilterTrigger").click(function () {
		if (commentFilterTriggerCount == 0) {
            $("#commentFilterSearch").css("display", "block");
            $(this).addClass("collapsed");
            commentFilterTriggerCount = 1;
        } else {
            $("#commentFilterSearch").css("display", "none");
            $(this).removeClass("collapsed");
            commentFilterTriggerCount = 0;
        }
    });

    $(".addFilterBtn").click(function () {
        $('#commentFilterSearch fieldset ul').append('<li><select class="input_select"><option>1</option><option>2</option></select><input type="text" class="input_text" /></li>');
        return false;
        $("#commentFilterSearch").css("height", "500px");
    });

    /**
     * Action to fire
     * when header filter buttons are triggresd
     */
    $('.status_filter li')
    .click(function (evt) {
        $(this).find('input').click().iff($.versionBetween(false,'1.6.0')).change();
    })
    .find('input')
        .click(function(evt){
            evt.stopPropagation();
        })
        .change(function(evt){
            if(!datatableCallback.loading) {
                datatableCallback.loading = true;
                datatableCallback.serverData[$(this).val()] = $(this).is(':checked');
                datatable.fnDraw();
            } else
                return false;
    }).end().find('label').click(function(evt){
        evt.stopPropagation();
    });
    
    /**
     * Action to fire
     * when header filter buttons are triggresd
     */
    $('.recommended_filter li')
    .click(function (evt) {
        $(this).find('input').click().iff($.versionBetween(false,'1.6.0')).change();
    })
    .find('input')
        .click(function(evt){
            evt.stopPropagation();
        })
        .change(function(evt){
            if(!datatableCallback.loading) {
                datatableCallback.loading = true;
                datatableCallback.serverData[$(this).val()] = $(this).is(':checked');
                datatable.fnDraw();
            } else
                return false;
    }).end().find('label').click(function(evt){
        evt.stopPropagation();
    });

    /**
     * Action to fire
     * when action select is triggered
     */
    $('.sort_tread').click(function () {
        var dir = $(this).find('span');
        if (dir.hasClass('ui-icon-triangle-1-n')) {
            dir.removeClass("ui-icon-triangle-1-n");
            dir.addClass('ui-icon-triangle-1-s');
            datatable.fnSort([
                [4, 'asc']
            ]);
        } else {
            dir.removeClass("ui-icon-triangle-1-s");
            dir.addClass('ui-icon-triangle-1-n');
            datatable.fnSort([
                [4, 'desc']
            ]);
        }
        dir.removeClass("ui-icon-carat-2-n-s");
    });
    $('.datatable .action').live('click', function () {
        var el = $(this);
        var id = el.attr('id');
        var ids = [id.match(/\d+/)[0]];
        var status = id.match(/[^_]+/)[0];

        if (status == 'deleted' && !confirm(putGS('You are about to permanently delete a comment.') + '\n' + putGS('Are you sure you want to do it?'))) {
            return false;
        }
        
        $.ajax({
            type: 'POST',
            url: 'comment/set-status/format/json',
            data: $.extend({
                "comment": ids,
                "status": status
            }, serverObj.security),
            success: function (data) {
                if ('deleted' == status) flashMessage(putGS('Comment deleted.'));
                else flashMessage(putGS('Comment status change to $1.', statusMap[status]));
                datatable.fnDraw(false);
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });

    });
    /**
     * Action to fire
     * when action submit is triggered
     */
    $('.dateCommentHolderEdit form,.dateCommentHolderReply form').live('submit', function () {
        var that = this;
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (data) {
                datatable.fnDraw();
                if (data['message'] == "successful") {
                    flashMessage(putGS('Comment updated.'));
                } else {
                    flashMessage(data['data']['subject'][0], "error");
                }
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
        return false;
    });
    $('.dateCommentHolderEdit .edit-cancel,.dateCommentHolderReply .reply-cancel').live('click', function () {
        var el = $(this);
        var td = el.parents('td');
        var form = el.parents('form');
        $(form).each(function () {
            this.reset();
        });
        td.find('.commentSubject,.commentBody').slideDown("fast");
        td.find('.content-edit').hide();
        td.find('.content-reply').hide();
    });

    $('.datatable .action-edit').live('click', function () {
        var el = $(this);
        var td = el.parents('td');
        td.find('.content-reply').hide();
        td.find('.commentSubject').toggle("fast");
        td.find('.commentBody').toggle("fast");
        td.find('.content-edit').toggle("fast");
    });

    $('.datatable .action-reply').live('click', function () {
        var el = $(this);
        var td = el.parents('td');
        td.find('.content-edit').hide();
        td.find('.content-reply').toggle("fast");
    });
    
    $('.datatable .action-recommend').live('click', function () {
        var el = $(this);
        var ids = [el.attr('id').replace('recommend_', '')];
        
        $.ajax({
            type: 'POST',
            url: 'comment/set-recommended/format/json',
            data: $.extend({
                'comment': ids,
                'recommended': 1
            }, serverObj.security),
            success: function (data) {
                flashMessage(putGS('Comment updated.'));
                datatable.fnDraw();
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
    });
    
    $('.datatable .action-unrecommend').live('click', function () {
        var el = $(this);
        var ids = [el.attr('id').replace('unrecommend_', '')];
        
        $.ajax({
            type: 'POST',
            url: 'comment/set-recommended/format/json',
            data: $.extend({
                'comment': ids,
                'recommended': 0
            }, serverObj.security),
            success: function (data) {
                flashMessage(putGS('Comment updated.'));
                datatable.fnDraw();
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
    });

    // Dialog
    var buttons = {};
    buttons[putGS('Close')] = function () {
        $(this).dialog("close");
    };

    $('.dialogPopup').dialog({
        autoOpen: false,
        width: 600,
        height: 560,
        position: 'right',
        buttons: buttons
    });
    // Dialog Link
    $('.articleLink').live('click', function () {
        var that = this;
        $.ajax({
            type: 'GET',
            url: $(this).attr('href'),
            success: function (data) {
				data = $.parseJSON(data);                
                var content = '<h3><a href="#">' + $(that).html() + '</a></h3>';
                for (i in data) {
                    content += '<h4>' + i + '</h4>';
                    content += '<p>' + data[i] + '</p>';
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
