/**
 * Functions for the image listing, used by images.php only	
 * @author $Author: paul $
 * @version $Id: images.js,v 1.1 2005/05/03 15:28:07 paul Exp $
 * @package ImageManager
 */

	function i18n(str) {
        return HTMLArea._lc(str, 'ImageManager');
	};

	function changeDir(newDir) 
	{
		showMessage('Loading');

		// backend_url is defined in the calling page. For now we 
		// assume it has a trailing &

		location.href = _backend_url + "__function=images&dir="+newDir;
	}


	function newFolder(dir, newDir) 
	{
		location.href = _backend_url + "__function=images&dir="+dir+"&newDir="+newDir;
	}

	//update the dir list in the parent window.
	function updateDir(newDir)
	{
		var selection = window.top.document.getElementById('dirPath');
		if(selection)
		{
			for(var i = 0; i < selection.length; i++)
			{
				var thisDir = selection.options[i].text;
				if(thisDir == newDir)
				{
					selection.selectedIndex = i;
					showMessage('Loading');
					break;
				}
			}		
		}
	}

	function selectImage(filename, alt, width, height) 
	{
		var topDoc = window.top.document;
		
		var obj = topDoc.getElementById('f_url');  obj.value = filename;
		var obj = topDoc.getElementById('f_width');  obj.value = width;
		var obj = topDoc.getElementById('f_width'); obj.value = width;
		var obj = topDoc.getElementById('f_height'); obj.value = height;
		var obj = topDoc.getElementById('f_alt'); obj.value = alt;
		var obj = topDoc.getElementById('orginal_width'); obj.value = width;
		var obj = topDoc.getElementById('orginal_height'); obj.value = height;		
	}

	function showMessage(newMessage) 
	{
		var topDoc = window.top.document;

		var message = topDoc.getElementById('message');
		var messages = topDoc.getElementById('messages');
		if(message && messages)
		{
			if(message.firstChild)
				message.removeChild(message.firstChild);

			message.appendChild(topDoc.createTextNode(i18n(newMessage)));
			
			messages.style.display = "block";
		}
	}

	function addEvent(obj, evType, fn)
	{ 
		if (obj.addEventListener) { obj.addEventListener(evType, fn, true); return true; } 
		else if (obj.attachEvent) {  var r = obj.attachEvent("on"+evType, fn);  return r;  } 
		else {  return false; } 
	} 

	function confirmDeleteFile(file) 
	{
		if(confirm(i18n("Delete file?")))
			return true;
	
		return false;		
	}

	function confirmDeleteDir(dir, count) 
	{
		if(count > 0)
		{
			alert(i18n("Please delete all files/folders inside the folder you wish to delete first."));
			return;
		}

		if(confirm(i18n("Delete folder?"))) 
			return true;

		return false;
	}

	addEvent(window, 'load', init);
