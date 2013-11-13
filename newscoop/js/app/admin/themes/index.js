newscoopDatatables =
{
	callbackServerData : function ( sSource, aoData, fnCallback ) 
	{
		$.ajax
		({
			dataType : 'json',
			type : "POST",
			url : sSource,
			data : aoData,
			success : fnCallback,
			error : function(xhr, textStatus, errorThrown)
			{
				if (xhr.getResponseHeader('Not-Logged-In')) {
					location.reload();
				}
			}
		});
	},
	callbackRow : function( row, data, displayIndex, displayIndexFull )
	{
		$(row).children().each( function( idx )
		{
			try
			{
				$( this ).tmpl( '#datatableTmpl_'+idx, data );
			}
			catch( e ){ if( typeof console != 'undefined' ) console.log( e ); }
		});
		
		$('.themesListTabs .imageItem', row ).each( function( i, e )
		{
            // copy href from image to link
			$( '.imageCtrls a', row ).eq(i).attr( 'href', $(e).find('a').attr('href') );

            // copy title from link to image
            $(e).find('a').attr('title', $('.imageCtrls a', row).eq(i).attr('title'));
		});

		$( '.imageCtrls a', row ).fancybox
		({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
            'titlePosition': 'inside',
			'speedIn'		:	600, 
			'speedOut'		:	200
		}) 

		return row;
	},
	callbackDraw : function()
	{
		$('.themesListTabs li a').mouseover( function()
		{
			var parentLi = $(this).parent( 'li' );
			parentLi.siblings().removeClass( 'ui-tabs-selected ui-state-active' );
			parentLi.addClass( 'ui-tabs-selected ui-state-active' );

			var idx = parentLi.parent('ul').find( 'li' ).index( parentLi );
			var imgs = $(this).parents( 'td' ).eq(0).prev().find( 'li' );
			imgs.addClass( 'ui-tabs-hide' ).eq( idx ).removeClass( 'ui-tabs-hide' );
		});

		$(".themesListTabs a").fancybox({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
            'titlePosition': 'inside',
			'speedIn'		:	600, 
			'speedOut'		:	200
        });

		$(".actionDropDown li").hover(function()
		{
			$(this).children("ul").css("display","block");
			$(this).children("a").addClass("active");
		}, function()
		{
			$(this).children("ul").css("display","none");
			$(this).children("a").removeClass("active");
		});
		$(".actionDropDown li ul li").hover(function()
		{
			$(this).children("ul").css("display","block");
		}, function()
		{
			$(this).children("ul").css("display","none");
		});

		try
		{
			$( document.body ).data( 'newscoop.themesDatatable.oneFilterCallback' ).apply( this );
			$( document.body ).removeData( 'newscoop.themesDatatable.oneFilterCallback' );
		}
		catch( e ){}
	}
}
$( function()
{
	// tabs functionallity
	$('.themesListHolder .themesListTabsBtns li a').click( function()
	{
		var parentLi = $(this).parent();
		parentLi.siblings().removeClass( 'ui-tabs-selected ui-state-active' );
		parentLi.addClass('ui-tabs-selected ui-state-active');

		var datatable = $( document.body ).data( 'newscoop.themesDatatable' );
		var pubId 	  = $(this).attr( 'pub-id' );
		if( pubId ) // publication tab
		{
			datatable.fnFilter( pubId, 0 ); // FILTERING PUBLICATION
			$( document.body ).data( 'newscoop.themesDatatable.oneFilterCallback', function()
			{
				$('.copy-to-avail-themes').show();
				$('.actionDropDown .edit:parent').show();
				$('.actionDropDown .unassign:parent').show();
				$('.actionDropDown .download:parent').show();
				$('.actionDropDown .delete:parent').hide();
				$('.actions-publications li a[pub-id='+pubId+']:parent').hide();
			});
		}
		else
		{
			datatable.fnSettings().aoPreSearchCols[0].sSearch = ""; // hacked, RESETTING PUBLICATION FILTER
			datatable.fnFilter( "" );
			$( document.body ).data( 'newscoop.themesDatatable.oneFilterCallback', function()
			{
				$('.actions-publications li a[pub-id]:parent').show()
				$('.copy-to-avail-themes').hide();
				$('.actionDropDown .unassign:parent').hide();
				$('.actionDropDown .download:parent').show();
				$('.actionDropDown .delete:parent').show();
				$('.actionDropDown .edit:parent').show();
			});
		}
	});
	// binding for assign and copy controls
	$('.assign-ctrl,.copy-to-avail-themes a').live( 'click', function( evt )
	{
		var thisA = $(this);
		$.ajax
		({
			url : thisA.attr('href')+'/format/json',
			dataType : "json",
			success : function( data ) {
				var msgCon = thisA.parents('ul.actionDropDown').prev('div')
								.fadeTo( 'fast', 1 )
								.text( ( data.exception ? data.exception.message : data.response ) );
				msgCon.delay(3000).fadeTo( 'fast', 0.01, function(){ $(this).html( '' ); })
			}
		})
		evt.preventDefault()
	});
	
	var confirmUnassignDialog = $('<div />')
	.tmpl( '#confirmUnassignTmpl', {} )
	.dialog
	({
		autoOpen: false,
		width: 460,
		resizable: false,
		modal: true,
		position:'center',
		title: $('#confirmUnassignTmpl').attr( 'title' )
	});
	
	
	$('.actionDropDown .unassign').live( 'click', function( evt )
			{
				var thisA = $(this)
				confirmUnassignDialog.dialog( 'option', 'buttons', 
				{
					"Unassign" : function()
					{
						$.ajax
						({
							url : thisA.attr('href')+'/format/json',
							dataType : "json",
							success : function( data ) 
							{
								if( data.response ) 
								{
                                    var oldtext = confirmUnassignDialog.find('.message-holder').text();
									confirmUnassignDialog.find( '.message-holder' ).html('<span class="' + data.status + '">' + data.response + '</span>')
										.show().delay(data.status ? 1000 : 3000).fadeOut('fast', function() {
                                            confirmUnassignDialog.find('.message-holder').text(oldtext);
											confirmUnassignDialog.dialog( 'close' ); 
                                            confirmUnassignDialog.find('.message-holder').show();
										});
									
									if( data.status ) {
										// TODO bad way to refresh datagrid
										$('.themesListHolder .themesListTabsBtns li a').parent( '.ui-state-active' ).find( 'a' ).click()
									}
								}
							}
						})
					},
					"Cancel" : function(){ $(this).dialog("close"); } 
				}).dialog( 'open' );
						
				
				evt.preventDefault()
			});
	
	
	var confirmDeleteDialog = $('<div />')
		.tmpl( '#confirmDeleteTmpl', {} )
		.dialog
		({
			autoOpen: false,
			width: 460,
			resizable: false,
			modal: true,
			position:'center',
			title: $('#confirmDeleteTmpl').attr( 'title' )
		});
	
		
	$('.actionDropDown .delete').live( 'click', function( evt )
	{
		var thisA = $(this)
		confirmDeleteDialog.dialog( 'option', 'buttons', 
		{
			"Delete" : function()
			{
				$.ajax
				({
					url : thisA.attr('href')+'/format/json',
					dataType : "json",
					success : function( data ) 
					{
						if( data.response ) 
						{
							confirmDeleteDialog.find( '.delete-message' ).text( data.response )
								.show().delay(data.status ? 1000 : 3000).fadeOut( 'fast', function()
								{
									confirmDeleteDialog.find( '.delete-message' ).text( '' );
									confirmDeleteDialog.dialog( 'close' ); 
								} );
							
							if( data.status ) {
								// TODO bad way to refresh datagrid
								$('.themesListHolder .themesListTabsBtns li a').parent( '.ui-state-active' ).find( 'a' ).click()
							}
						}
					}
				})
			},
			"Cancel" : function(){ $(this).dialog("close"); } 
		}).dialog( 'open' );
				
		
		evt.preventDefault()
	});
	
	$('.actions .navigation .upload').click( function()
	{
		var uploadDiv = $('<div />') // gonna remake this so we have a fresh iframe and form input without any stuff left over. u never know
			.tmpl( '#popupTmpl', {} )
			.dialog
			({
				autoOpen: true,
				width: 460,
				resizable: false,
				modal: true,
				position:'center',
				title: $('#popupTmpl').attr( 'title' ),
				buttons: 
				{
					"Import" : function() 
					{ 
						uploadDiv.find( 'form' ).trigger( 'submit' );
						uploadDiv.find( 'iframe' ).load( function()
						{
							var data = $(this).contents().find('body').text();
							if( $.trim( data ) != "" )
							{
								eval( "data = " + data );
								if( data.response )	{
									uploadDiv.find( '.upload-success' )
										.show().delay(3000).fadeOut( 'fast', function(){ uploadDiv.dialog( 'close' ); } );
									// TODO bad way to refresh datagrid
									$('.themesListHolder .themesListTabsBtns li a').parent( '.ui-state-active' ).find( 'a' ).click()
								}
								else {
									uploadDiv.find( '.upload-error' )
										.show().delay(3000).fadeOut( 'fast' );;
								}
							}
						});
					}, 
					"Cancel" : function() 
					{ 
						$(this).dialog("close"); 
					} 
				},
				close : function()
				{
					uploadDiv.dialog( 'destroy' ).remove();	
				}
			})
			.dialog( 'open' );
		
	})
});
