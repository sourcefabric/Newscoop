(function($)
{
    function versionNumber(version){
        version = parseInt(version.replace(/\./g,''));
        version = version>999? version : version*10;
        return version;
    }
    $.extend
    ({
        versionCurrent: versionNumber( $.fn.jquery ),
        versionBetween: function(less, greater)
        {
          var between = less? $.versionCurrent>versionNumber(less): true;
          between = between && (greater? $.versionCurrent<versionNumber(greater): true);
          return between;
        }
    });
})(jQuery);