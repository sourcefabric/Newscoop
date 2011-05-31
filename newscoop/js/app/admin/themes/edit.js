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
	
	$('.templateSettings form').live( 'submit', function( evt )
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
		
	$('.matchWithExisting ul li div input').click(function()
	{
		if (this.checked) 
		{
			$(this).parent().parent().find("div").removeClass('active');
			$(this).parent().addClass('active');
			$(this).parent().parent().find("select").attr('disabled', 'disabled');
			$(this).parent().parent().find("div input").attr('checked', false);
			$(this).attr('checked', true);
		} 
		else 
		{
			$(this).parent().removeClass('active');
			$(this).parent().parent().find("select").removeAttr('disabled');
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