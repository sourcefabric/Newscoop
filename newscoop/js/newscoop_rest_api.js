/**
 * MicroAjax (http://code.google.com/p/microajax/)
 */
function microAjax(B,A){this.bindFunction=function(E,D){return function(){return E.apply(D,[D])}};this.stateChange=function(D){if(this.request.readyState==4){this.callbackFunction(this.request.responseText)}};this.getRequest=function(){if(window.ActiveXObject){return new ActiveXObject("Microsoft.XMLHTTP")}else{if(window.XMLHttpRequest){return new XMLHttpRequest()}}return false};this.postBody=(arguments[2]||"");this.callbackFunction=A;this.url=B;this.request=this.getRequest();if(this.request){var C=this.request;C.onreadystatechange=this.bindFunction(this.stateChange,this);if(this.postBody!==""){C.open("POST",B,true);C.setRequestHeader("X-Requested-With","XMLHttpRequest");C.setRequestHeader("Content-type","application/json");C.setRequestHeader("Connection","close")}else{C.open("GET",B,true)}C.send(this.postBody)}};

(function () {
    var apiEndpoint;
    var base_path;

    /**
     * Newscoop REST API - JavaScript SDK
     */
    var NewscoopRestApi = function(ap)
    {   
        apiEndpoint = ap;

        return NewscoopRestApi.prototype.apiCallBuilder;
    };

    /**
     * Utils 
     * @type Object
     */
    NewscoopRestApi.prototype.utils = {
        /**
         * Build proper uri string
         * @param  object params Optional parameters
         * @return string        Proper uri string
         */
        buildUrl: function(params)
        {
            /**
             * Construct the base url
             */
            var url = apiEndpoint + base_path;
            
            /**
             * Assign key/Value pairs to GET String
             */
            var pairs = [];

            for(var key in params)
            {
                if(!params.hasOwnProperty(key))
                {
                    continue;
                }
                
                pairs.push(encodeURIComponent(key) + '=' + encodeURIComponent(params[key]));
            }
            
            if(pairs.length > 0)
            {
                return url + '?' + pairs.join('&');
            }
            
            return url;
        }
    }

    /**
     * Set of helper methods for build proper resource uri
     * @type Object
     */
    NewscoopRestApi.prototype.apiCallBuilder = {
        params: null,

        /**
         * Choose resource
         * @param  string path      resource oath
         * @param  object newParams optional parameters
         * @return Object           NewscoopRestApi.apiCallBuilder object
         */
        getResource: function(path, newParams)
        {
            base_path = path;

            if (typeof newParams != 'undefined') {
                this.params = newParams;
            }

            return this;
        },

        /**
         * Add new parameter to parrams array
         * @param  string key   parameter name
         * @param  string value parameter value
         * @return Object       NewscoopRestApi.apiCallBuilder object
         */
        addParam: function(key, value)
        {   
            if (this.params === null) {
                this.params = [];
            }

            this.params[key] = value;

            return this;
        },

        /**
         * Set number of returned items
         * @param  integer number Number of items
         * @return Object         NewscoopRestApi.apiCallBuilder object
         */
        setItemsPerPage: function(number)
        {
            this.addParam('items_per_page', number);

            return this;
        },

        /**
         * Requested page number
         * @param  integer page Page number
         * @return Object       NewscoopRestApi.apiCallBuilder object
         */
        setPage: function(page)
        {
            this.addParam('page', page);

            return this;
        },

        /**
         * Set order
         * @param  array  order  Key in array is field name and value is direction
         * @return Object        NewscoopRestApi.apiCallBuilder object
         */
        setOrder: function(order)
        {
            for (var key in order) {
                if (order.hasOwnProperty(key)) {
                    this.addParam('sort['+key+']', order[key]);
                }
            }
            return this;
        },

        /**
         * Set choosen fields
         * @param  array fields  Arrray with chosen fields
         * @return Object        NewscoopRestApi.apiCallBuilder object
         */
        setFields: function(fields)
        {
            fields = [].concat(fields);

            this.addParam('fields', fields.join(','));

            return this;
        },

        /**
         * Call NewscoopRestApi.makeRequest with callback
         * @param  Function callback Callback with json object as parameter
         */
        makeRequest: function(callback)
        {   
            NewscoopRestApi.prototype.makeRequest(NewscoopRestApi.prototype.utils.buildUrl(this.params), callback)
        }
    }

    /**
     * Make ajax request to choosen uri and run callback with data 
     * @param  string   uri      choosen uri and
     * @param  Function callback Callback with json object as parameter
     */
    NewscoopRestApi.prototype.makeRequest = function(uri, callback)
    {
        microAjax(uri, function(res){
            if (typeof callback != 'undefined') {
                callback(eval("(" + res + ')'))
            }
        });
    }

    window.NewscoopRestApi = NewscoopRestApi;
})();