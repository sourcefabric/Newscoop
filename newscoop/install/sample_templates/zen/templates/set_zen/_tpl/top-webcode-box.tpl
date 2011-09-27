<script type="text/javascript">
function handleEnter(inField, e) {
    var charCode;
    
    if(e && e.which){
        charCode = e.which;
    }else if(window.event){
        e = window.event;
        charCode = e.keyCode;
    }

    if(charCode == 13) {
        var webcode = inField.value;
        var location = 'http://{{ $gimme->publication->site }}/' + webcode;
        window.location = location;
    }
}
</script>
<div class="webcode-box" style="display:box; float:right; width:325px">
{{ $gimme->publication->site }}/<input type="text" placeholder="Webcode" onkeypress="handleEnter(this, event)" style="float: right"/>
</div>