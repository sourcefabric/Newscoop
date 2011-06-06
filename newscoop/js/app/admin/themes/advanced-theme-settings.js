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
				if( data.success ) void(0);
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
		$('.templateSettings form,.versionHolder form, .articleTypes form').trigger('submit');
	});
	
	/**
	 * change article type selects handler
	 */
	$('select.articleType').change( function()
	{
		var typesSelect = $(this).siblings( 'ul' ).find( 'select' ).html( '' );
		var articleTypes = $.registry.get( 'articleTypes' )[$(this).val()];
		var themeArticleTypes = $.registry.get( 'themeArticleTypes' );
		
		/*
		var searchArticleTypes = null;
		
		for( i in themeArticleTypes ) // test match article type with theme type 
		{
			if(	i.toLowerCase() == $(this).attr( 'name' ).match( /\[(.+)\]/ )[1].toLowerCase() 
				&& $(this).val().toLowerCase() == i.toLowerCase() ) {
				searchArticleTypes = themeArticleTypes[i];
			}
		}
		*/
		
		for( i in  articleTypes ) 
		{
			var opt = $( '<option />' ).val( articleTypes[i] ).text( articleTypes[i].replace( '_', ' ' ).capitalize() );
			typesSelect.append( opt ); 
		}
	}).trigger( 'change' );
	
	// Tabs
	$('.tabs, .themeSettingsTabs').tabs();
	$('.tabs').tabs('select', '#tabs-1');
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