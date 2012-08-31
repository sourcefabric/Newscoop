/**
 * Document model
 */
var Document = Backbone.Model.extend({
    day: 3600 * 24,
    month: 3600 * 24 * 30,
    year: 3600 * 24 * 365,
    date: /(20[0-2][0-9])-([0-1][0-9])-([0-3][0-9])T([0-2][0-9]):([0-5][0-9]):([0-5][0-9])Z/,

    types: {
        'news': 'omni',
        'newswire': 'article',
        'dossier': 'dossier',
        'user': 'user',
        'tweet': 'twitter',
        'event': 'event',
        'comment': 'comment',
        'link': 'link',
        'blog': 'omni'
    },

    /**
     * Get relative date
     *
     * @param {string} property
     * @return {string}
     */
    relDate: function(property) {
        var published = this.parseUtcDate(this.get(property));
        if (!published) {
            return '';
        }

        published = published.getTime();
        var now = (new Date()).getTime();
        var diff = Math.ceil(Math.abs(now - published) / 1000);

        if (diff < 60) {
            return 'vor ' + diff + ' Sek.';
        } else if (diff < 3600) {
            return 'vor ' + (diff / 60).toFixed() + ' Min.';
        } else if (diff < this.day) {
            return 'vor ' + (diff / 3600).toFixed() + ' Std.';
        } else if (diff < this.month) {
            var days = (diff / this.day).toFixed();
            return 'vor ' + days + ' ' + (days == 1 ? 'Tag' : 'Tagen');
        } else if (diff < this.year) {
            var months = (diff / this.month).toFixed();
            return 'vor ' + months + ' ' + (months == 1 ? 'Monat' : 'Monaten');
        } else {
            var years = (diff / this.year).toFixed();
            return 'vor ' + years + ' ' + (years == 1 ? 'Jahr' : 'Jahren');
        }
    },

    /**
     * Parse given utc time and returns date object
     *
     * @param {string} datestring
     * @return {Date}
     */
    parseUtcDate: function(datestring) {
        var utc = this.date.exec(datestring);
        if (!utc) {
            return null;
        }

        var date = new Date();
        date.setUTCFullYear(utc[1]);
        date.setUTCMonth(utc[2] - 1);
        date.setUTCDate(utc[3]);
        date.setUTCHours(utc[4]);
        date.setUTCMinutes(utc[5]);
        date.setUTCSeconds(utc[6]);
        return date;
    },

    /**
     * Get template type
     *
     * @return {string}
     */
    getTemplateType: function() {
        if (!this.get('type') in this.types) {
            console.log(this.get('type'));
        } else {
            return this.types[this.get('type')];
        }
    },

    /**
     * Get tweet
     *
     * @return {string}
     */
    getTweet: function() {
        var tweet = this.get('tweet');
        if (!tweet) {
            return tweet;
        }

        return tweet.replace(/(http:\/\/t.co\/[\w]+)/, '<a href="$1" rel="nofollow" target="_blank">$1</a>');
    },

    /**
     * Get event date
     *
     * @return {string}
     */
    getEventDate: function() {
        return this.get('event_date') ? this.get('event_date') : '';
    },

    /**
     * Get event time
     *
     * @return {string}
     */
    getEventTime: function() {
        return this.get('event_time') ? this.get('event_time') + ' Uhr' : '';
    },

    /**
     * Get event title
     *
     * @return {string}
     */
    getEventTitle: function() {
        var title = this.escape('title');
        return title.replace(/ \([-0-9]+\)$/, '');
    },

    /**
     * Get publication id
     *
     * @return {string}
     */
    getPublicationId: function() {
        if (this.get('type') === 'blog') {
            return '5';
        } else if (this.get('type') === 'event') {
            return '4';
        } else {
            return '1';
        }
    },

    /**
     * Get article number
     *
     * @return {int}
     */
    getArticleNumber: function()
    {
        return this.get('id').split('-')[1];
    }
});

/**
 * Document collection
 */
var DocumentCollection = Backbone.Collection.extend({
    model: Document,

    topics: {},

    buildParams: function() {
        var params = {};

        if (this.query) {
            params.q = this.query;
        }

        if (this.type) {
            params.type = this.type;
        }

        if (this.date) {
            params.date = this.date;
        }

        if (this.start) {
            params.start = this.start;
        }

        if (this.sortf) {
            params.sort = this.sortf;
        }

        if (this.source) {
            params.source = this.source;
        }

        if (this.section) {
            params.section = this.section;
        }

        if (this.topics.length) {
            params.topic = this.topics.join();
        }

        return params;
    },

    url: function() {
        var params = this.buildParams();
        params.format = 'json';
        return '?' + jQuery.param(params);
    },

    nav: function() {
        var params = this.buildParams();
        return '?' + jQuery.param(params);
    },

    parse: function(response) {
        this.response = response;
        this.query = response.responseHeader.params.q;
        this.count = parseInt(response.response.numFound);
        this.start = parseInt(response.response.start);
        this.rows = parseInt(response.responseHeader.params.rows);
        this.type = response.responseHeader.params.type;
        this.date = response.responseHeader.params.date;
        this.source = response.responseHeader.params.source;
        this.section = response.responseHeader.params.section;
        this.facets = this.parseFacetFields(response);
        this.sortf = response.responseHeader.params.sort;
        this.topics = response.responseHeader.params.topic;
        this.topic = response.responseHeader.params.q_topic;
        return response.response.docs;
    },

    /**
     * Test if there are suggestions for query
     *
     * @return {bool}
     */
    hasSuggestion: function() {
        return this.response.spellcheck && this.response.spellcheck.suggestions.length !== 0;
    },

    /**
     * Get suggestion for query
     *
     * @return {string}
     */
    getSuggestion: function() {
        return this.response.spellcheck.suggestions[3];
    },

    /**
     * Parse facet fields
     *
     * @param {object} response
     * @return {object}
     */
    parseFacetFields: function(response) {
        if (!response.facet_counts) {
            return {};
        }

        var facets = {};
        var fields = response.facet_counts.facet_fields.type ? response.facet_counts.facet_fields.type : response.facet_counts.facet_fields.section;
        for (var i = 0; i < fields.length; i += 2) {
            facets[fields[i]] = fields[i + 1];
        }

        facets['article'] = facets['news'] + facets['newswire'];
        return facets;
    },

    /**
     * Toggle topic
     *
     * @param {string} topic
     * @return {void}
     */
    toggleTopic: function(topic) {
        var index = _.indexOf(this.topics, topic);
        if (index !== -1) {
            this.topics = [];
        } else {
            this.topics = [topic];
        }
    }
});
