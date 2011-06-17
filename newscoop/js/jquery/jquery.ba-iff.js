/*!
 * iff - v0.2 - 6/3/2009
 * http://benalman.com/projects/jquery-iff-plugin/
 *
 * Copyright (c) 2009 "Cowboy" Ben Alman
 * Licensed under the MIT license
 * http://benalman.com/about/license/
 */

(function($){
    '$:nomunge'; // Used by YUI compressor.

    $.fn.iff = function( test ) {
        var failed = !test || $.isFunction( test)
                && !test.apply( this, Array.prototype.slice.call(arguments, 1) ),
            $new = this.pushStack( failed ? [] : this, 'iff', [test] );

        $new.els = function() {
            return this.end().pushStack(
                failed ? this.prevObject : [],
                this.selector.substr(this.prevObject.selector.length + 1) + '.else',
                []
            );
        };

        return $new;
    };

    $.fn.els = function() { return this; };

})(jQuery);