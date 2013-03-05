var statusMap = {
    'saved': 'submitted',
    'published': 'published',
    'deleted': 'deleted'
};

var datatableCallback = {
    serverData: {},
    loading: false,
    addServerData: function (sSource, aoData, fnCallback) {
        that = datatableCallback;
        for (i in that.serverData) {
            console.log(aoData);

            if (i == 'submitted' || i == 'published' || i == 'deleted') {
                if (that.serverData[i]) {
                    aoData.push({
                        "name": "sFilter[status][]",
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
            .addClass('status_' + statusMap[aData.notice.status])
            .tmpl('#notice-tmpl', aData)
            //.find("input."+ statusMap[aData.notice.status]).attr("checked","checked");
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
                                    <option value="submitted">' + putGS('Submitted') + '</option>\
                                    <option value="published">' + putGS('Published') + '</option>\
                                    <option value="deleted">' + putGS('Deleted')+ '</option>\
                                </select>\
                                <div id="manageLinksTarget" style="float:right"></div>\
                              </fieldset>'

        );
        var manageLinks = $('#manageLinks').remove();
        manageLinks.appendTo('#manageLinksTarget');

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
                
                
                if (status == 'deleted' && !confirm(putGS('You are about to permanently delete multiple notics.') + '\n' + putGS('Are you sure you want to do it?'))) {
                    return false;
                }
                
                $.ajax({
                    type: 'POST',
                    url: 'notice/set-status/format/json',
                    data: $.extend({
                        "notice": ids,
                        "status": status
                    }, serverObj.security),
                    success: function (data) {
                        flashMessage(putGS('Notice status change to $1.', statusMap[status]));
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

        if (status == 'deleted' && !confirm(putGS('You are about to permanently delete a Notice.') + '\n' + putGS('Are you sure you want to do it?'))) {
            return false;
        }
        
        $.ajax({
            type: 'POST',
            url: 'notice/set-status/format/json',
            data: $.extend({
                "notice": ids,
                "status": status
            }, serverObj.security),
            success: function (data) {
                if ('deleted' == status) flashMessage(putGS('Notice deleted.'));
                else flashMessage(putGS('Notice status change to $1.', statusMap[status]));
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
                flashMessage(putGS('Notice updated.'));
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
