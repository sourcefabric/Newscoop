var statusMap = {
    'pending': 'new',
    'processed': 'processed',
    'starred': 'starred',
    'deleted': 'deleted'
};
var attachmentTypeMap = {
    'none': 'none',
    'image': 'image',
    'document': 'document'
};
var datatableCallback = {
    serverData: {},
    loading: false,
    addServerData: function (sSource, aoData, fnCallback) {
        that = datatableCallback;
        for (i in that.serverData) {
            if (i == 'pending' || i == 'processed' || i == 'starred' || i == 'deleted') {
				if (that.serverData[i]) {
					aoData.push({
						"name": "sFilter[status][]",
						"value": i
					});
				}
			}
			else {
				if (that.serverData[i]) {
					aoData.push({
						"name": "sFilter[attachmentType][]",
						"value": i
					});
				}
			}
        }
        $.getJSON(sSource, aoData, function (json) {
            fnCallback(json);
        });
    },
    row: function (nRow, aData, iDisplayIndex, iDisplayIndexFull) {
        $(nRow)
            .addClass('attachment_type_' + attachmentTypeMap[aData.message.attachmentType])
            .addClass('status_' + statusMap[aData.message.status])
            .tmpl('#comment-tmpl', aData)
            .find("input."+ statusMap[aData.message.status]).attr("checked","checked");
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
                                  <option value="">' + putGS('Change selected messages status') + '</option>\
                                  <option value="pending">' + putGS('New') + '</option>\
                                  <option value="processed">' + putGS('Processed') + '</option>\
                                  <option value="starred">' + putGS('Starred') + '</option>\
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
                
                
                if (status == 'deleted' && !confirm(putGS('You are about to permanently delete multiple messages.') + '\n' + putGS('Are you sure you want to do it?'))) {
                    return false;
                }
                
                $.ajax({
                    type: 'POST',
                    url: 'feedback/set-status/format/json',
                    data: $.extend({
                        "feedback": ids,
                        "status": status
                    }, serverObj.security),
                    success: function (data) {
                        flashMessage(putGS('Messages status change to $1.', statusMap[status]));
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
     * when header filter buttons are triggered
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
     * when header filter buttons are triggered
     */
    $('.attachment_filter li')
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
    $('.datatable .action').live('click', function () {
        var el = $(this);
        var id = el.attr('id');
        var ids = [id.match(/\d+/)[0]];
        var status = id.match(/[^_]+/)[0];

        if (status == 'deleted' && !confirm(putGS('You are about to permanently delete a message.') + '\n' + putGS('Are you sure you want to do it?'))) {
            return false;
        }

        $.ajax({
            type: 'POST',
            url: 'feedback/set-status/format/json',
            data: $.extend({
                "feedback": ids,
                "status": status
            }, serverObj.security),
            success: function (data) {
                if ('deleted' == status) flashMessage(putGS('Message deleted.'));
                else flashMessage(putGS('Message status change to $1.', statusMap[status]));
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
    $('.approval form').live('submit', function () {
        var that = this;
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (data) {
                flashMessage(putGS('Message updated.'));
                datatable.fnDraw();
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
        return false;
    });
    /**
     * Action to fire
     * when action submit is triggered
     */
    $('.dateCommentHolderReply form').live('submit', function () {
        var that = this;
        $.ajax({
            type: 'POST',
            url: $(this).attr('action'),
            data: $(this).serialize(),
            success: function (data) {
                datatable.fnDraw();
                flashMessage(putGS('Message updated.'));
            },
            error: function (rq, status, error) {
                if (status == 0 || status == -1) {
                    flashMessage(putGS('Unable to reach Newscoop. Please check your internet connection.'), "error");
                }
            }
        });
        return false;
    });
    $('.dateCommentHolderReply .reply-cancel').live('click', function () {
        var el = $(this);
        var td = el.parents('td');
        var form = el.parents('form');
        $(form).each(function () {
            this.reset();
        });
        td.find('.commentSubject,.commentBody').slideDown("fast");
        td.find('.content-reply').hide();
    });

    $('.datatable .action-reply').live('click', function () {
        var el = $(this);
        var td = el.parents('td');
        td.find('.content-reply').toggle("fast");
    });
    // Dialog
    $('.dialogPopup').dialog({
        autoOpen: false,
        width: 600,
        height: 560,
        position: 'right',
        buttons: {
            "Cancel": function () {
                $(this).dialog("close");
            }
        }
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
                else {
				}
            }
        });
        return false;
    });
});
