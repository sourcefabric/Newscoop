newscoopDatatables =
{
	callbackRow : function( row, data, displayIndex, displayIndexFull )
	{
		$(row).children().each( function( idx )
		{
			try
			{
				$( this ).tmpl( '#datatableTmpl_'+idx, data );
			}
			catch( e ){ if( console ) console.log( e ); }
		});
		
		$('.themesListTabs .imageItem', row ).each( function( i, e )
		{
			$( '.imageCtrls a', row ).eq(i).attr( 'href', $(e).find('a').attr('href') );
		});

		$( '.imageCtrls a', row ).fancybox
		({
			'transitionIn'	:	'elastic',
			'transitionOut'	:	'elastic',
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

		$(".themesListTabs div a").fancybox();

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
			$(this).childer("ul").css("display","none");
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
			datatable.fnFilter( pubId, 4 );
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
			datatable.fnSettings().aoPreSearchCols[4].sSearch = ""; // hacked
			datatable.fnFilter( "" );
			$( document.body ).data( 'newscoop.themesDatatable.oneFilterCallback', function()
			{
				$('.actions-publications li a[pub-id]:parent').show()
				$('.copy-to-avail-themes').hide();
				$('.actionDropDown .unassign:parent').hide();
				$('.actionDropDown .download:parent').hide();
				$('.actionDropDown .delete:parent').show();
				$('.actionDropDown .edit:parent').hide();
			});
		}
	})

	$('.assign-ctrl').live( 'click', function( evt )
	{
		var thisA = $(this)
		$.ajax
		({
			url : $(this).attr('href')+'/format/json',
			success : function( data ) {
				var msgCon = thisA.parents('ul.actionDropDown').prev('div')
								.fadeTo( 'fast', 1 )
								.text( ( data.exception ? data.exception.message : data.response ) );
				msgCon.delay(3000).fadeTo( 'fast', 0.01, function(){ $(this).html( '' ); })
			}
		})
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
	
		
	$('.actionDropDown .unassign, .actionDropDown .delete').live( 'click', function( evt )
	{
		var thisA = $(this)
		confirmDeleteDialog.dialog( 'option', 'buttons', 
		{
			"Delete" : function()
			{
				$.ajax
				({
					url : thisA.attr('href')+'/format/json',
					success : function( data ) 
					{
						if( data.response ) 
						{
							confirmDeleteDialog.find( '.delete-message' ).text( data.response )
								.show().delay(3000).fadeOut( 'fast', function()
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
					"Upload" : function() 
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