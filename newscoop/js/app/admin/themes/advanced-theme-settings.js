jQuery( function()
{
	$('.themeSettingsTabsNav li a').click( function()
	{
		var thisA = $(this);
		$.ajax
		({ 
			url : $.registry
					.get( 'load-output-settings-url' )
					.replace( '$1', thisA.attr( 'theme-id' ) )
					.replace( '$2', thisA.attr( 'output-id' ) ),
			success : function(data)
			{
				$('#themeSettingsTab .form-container').html(data);
			}
		});
	})
	.eq(0).click();
	
	/**
	 * add ajax to form submits
	 */
	$('.templateSettings form, .articleTypes form').live( 'submit', function( evt )
	{
		var thisForm = $(this)
		$.ajax
		({
			url : thisForm.attr( 'action' ) + '?format=json',
			type : thisForm.attr( 'method' ),
			data : thisForm.serialize(),
			dataType : 'json',
			success : function( data )
			{
				$('#submit-settings-ctrl').trigger( 'form-submitted.newscoop' );
			}
		})
		evt.preventDefault();
	})
	$('.articleTypes form').submit( function()
	{
		$(this).find('select').removeAttr( 'disabled' ); 
	})
	$('.versionHolder form').submit( function(){ return false; })
	
	$('#submit-settings-ctrl').click( function()
	{
		$('.templateSettings form, .versionHolder form, .articleTypes form').trigger('submit');
		$(this).val( $(this).attr( 'alt' ) );
		// set reload on all ajax completed
		$(this).data( 'form-submitted', 2 );
		$(this).bind( 'form-submitted.newscoop', function()
		{ 
			var frmSub = $(this).data( 'form-submitted' );
			if( frmSub == 1 ) {
				location.reload();
			}
			$(this).data( 'form-submitted', frmSub-1 );
		} )
	});
	
	/**
	 * change article type select's handler
	 */
	$('select.articleType').change( function()
	{
		var fieldsSelect = $(this).siblings( 'ul' ).find( 'select' ).html( '' );
		var articleTypes = $.registry.get( 'articleTypes' )[$(this).val()];
		var themeArticleTypes = $.registry.get( 'themeArticleTypes' );
		
		var typeIndex = $(this).parents('li:eq(0)').index(  );

		var i = 0, found = 0;
		var foundFields = [];
		for( k in themeArticleTypes ) // find matching fields
		{
			if( i++ == typeIndex ) 
			{
				found = themeArticleTypes[k];
				for( j in found )
					foundFields.push(j)
			}
		}
		fieldsSelect.each( function( fieldsIdx )
		{
			var selectedVal = 0;
			for( i in  articleTypes )  // put options on the selects
			{
				var opt = $( '<option />' )
					.val( articleTypes[i] )
					.text( articleTypes[i].replace( '_', ' ' ).capitalize() );
				if( articleTypes[i] == foundFields[fieldsIdx] )	{
					selectedVal = articleTypes[i];
				}
				$(this).append( opt );
			}
			$(this).val( selectedVal )
		})
		
	}).trigger( 'change' );
	
	// Tabs
	$('.tabs, .themeSettingsTabs').tabs({ cookie: { expires: 30 }});
    // BUTTONS
    $('.fg-button').hover
   	(
    	function(){ $(this).removeClass('ui-state-default').addClass('ui-state-focus'); },
    	function(){ $(this).removeClass('ui-state-focus').addClass('ui-state-default'); }
	);
	$('.icon-button').hover
	(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
	$('.text-button').hover
	(
		function() { $(this).addClass('ui-state-hover'); }, 
		function() { $(this).removeClass('ui-state-hover'); }
	);
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
		
	$('.matchWithExisting ul li div input').click( function()
	{
		var parentLi  = $(this).parents( 'li:eq(0)' );
		var parentDiv = $(this).parent(); 
		parentLi.find( 'div' ).removeClass( 'active' );
		if( $(this).attr( 'checked' ) ) 
		{
			parentDiv.addClass( 'active' );
			parentDiv.nextAll( 'select' ).attr( 'disabled', 'disabled' );
			parentDiv.siblings( 'div' ).find( 'input' ).removeAttr( 'checked' );
		} 
		else 
		{
			parentDiv.removeClass( 'active' );
			$(this).removeAttr( 'checked' );
			parentDiv.nextAll( 'select' ).removeAttr( 'disabled' );
		}
	});
	
	$('.toggle-button').click(function() 
	{
		$('.closeable').toggle('fast');
		$(this).toggleClass("closed");
		return false;
	}).next().hide();
	$('.toggle-link').click(function() 
	{
		$('.more-options').toggle('fast');
		$(this).toggleClass("opened");
		return false;
	}).next().hide();
	
});
