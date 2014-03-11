
    function  Save_Button_onclick() {
        var code =  WrapCode();
        code = code + document.getElementById("CodeArea").value;
        code = code + "</div> "
        if (document.getElementById("CodeArea").value == ''){
            tinyMCEPopup.close();
            return false;
        }
        tinyMCEPopup.execCommand('mceInsertContent', false, code);
        tinyMCEPopup.close();
    }

    function  WrapCode()
    {
        return "<div class=\"camp-textbox\">";
    }

    function Cancel_Button_onclick()
    {
        tinyMCEPopup.close();
        return false;
    }
