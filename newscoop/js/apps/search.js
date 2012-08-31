/**
 * Document view
 */
var DocumentView = Backbone.View.extend({
    tagName: 'li',

    initialize: function() {
        this.template = _.template($('#document-' + this.model.getTemplateType() + '-template').html());
    },

    render: function() {
        $(this.el).html(this.template({doc: this.model})).addClass(this.model.getTemplateType());
        return this;
    }
});

/**
 * Search form view
 */
var SearchFormView = Backbone.View.extend({
    events: {
        'blur input': 'search',
        'keypress input': 'search',
        'click button': 'search',
        'send': 'search'
    },

    webcode: /^[\+\@][a-z]{5,6}$/i,

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('input').val(this.collection.query);
    },

    search: function(e) {
        if (e.keyCode && e.keyCode !== 13) {
            return e;
        }

        e.preventDefault();

        this.collection.query = $(this.el).find('input').val();
        if (this.collection.query.match(this.webcode)) {
            window.location = this.options.webcodeUrl + this.collection.query;
        }

        this.collection.type = null;
        this.collection.date = null;
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Document list view
 */
var DocumentListView = Backbone.View.extend({
    events: {
        'click #did-you-mean': 'didYouMean'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
        this.emptyTemplate = _.template(this.options.emptyTemplate.html());
    },

    render: function() {
        var list = $(this.el).empty();
        $('#search-pagination').show();
        this.collection.each(function(doc) {
            var view = new DocumentView({model: doc});
            list.append(view.render().el);
        });

        if (this.collection.count < 13) {
            $('#search-pagination').hide();
        }

        if (this.collection.count === 0) {
            $("<li />").html(this.emptyTemplate()).appendTo($(this.el));
        }

        if (this.collection.hasSuggestion()) {
            $('<li id="spellcheck">Meinten Sie: <a href="#"></a>?</li>').prependTo(list).find('a').text(this.collection.getSuggestion()).attr('id', 'did-you-mean');
        } else {
            $(this.el).find('#spellcheck').detach();
        }

        if (this.collection.query) {
            $('head > title').text(this.collection.query + ' | Tageswoche');
        }

        if (this.collection.topic) {
            var link = $('<a />').text('Themen ' + this.collection.topic).attr('href', this.options.topicLink.replace('{topic}', this.collection.topic));
            $('<li />').prepend(link).prependTo(list);
        }

        return this;
    },

    didYouMean: function(e) {
        e.preventDefault();
        $('#search-query').val(this.collection.getSuggestion());
        $('#search-query').next('button').click();
    }
});

/**
 * Base filter view
 */
var FilterView = Backbone.View.extend({
    /**
     * Disable filter for given item
     *
     * @param {object} li
     * @return {void}
     */
    disableFilter: function(li) {
        li.find('a').hide();
        li.find('span').detach();
        $('<span />').text(li.find('a').text()).appendTo(li);
    },

    /**
     * Enable filter for given item
     *
     * @param {object} li
     * @return {void}
     */
    enableFilter: function(li) {
        li.find('span').detach();
        li.find('a').show();
    }
});

/**
 * Type filter view
 */
var TypeFilterView = FilterView.extend({
    events: {
        'click a': 'filter'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('li').removeClass('main');
        if (!this.collection.type) {
            $(this.el).find('li').first().addClass('main');
        } else {
            $(this.el).find('a[href="#' + this.collection.type + '"]').closest('li').addClass('main');
        }

        var facets = this.collection.facets;
        var view = this;
        $(this.el).find('a').not(':first').each(function() {
            var type = $(this).attr('href').slice(1);
            if (!facets[type]) {
                view.disableFilter($(this).closest('li'));
            } else {
                view.enableFilter($(this).closest('li'));
            }
        });
    },

    filter: function(e) {
        e.preventDefault();
        this.collection.type = e.target.hash.slice(1);
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Date filter view
 */
var DateFilterView = FilterView.extend({
    events: {
        'click a': 'filter',
        'click input[type=submit]': 'filterRange'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('li').removeClass('main');
        if (!this.collection.date) {
            $(this.el).find('li').first().addClass('main');
        } else {
            if (this.collection.date.indexOf(",") === -1) {
                $(this.el).find('a[href="#' + this.collection.date + '"]').closest('li').addClass('main');
            } else {
                var from = this.collection.date.split(",")[0];
                var to = this.collection.date.split(",")[1];

                if (from) {
                    $(this.el).find('#range_from').val(from).closest('li').addClass('main');
                }

                if (to) {
                    $(this.el).find('#range_to').val(to).closest('li').addClass('main');
                }
            }
        }
    },

    filter: function(e) {
        e.preventDefault();
        $(this.el).find('#range_from').val(null);
        $(this.el).find('#range_to').val(null);
        this.collection.date = e.target.hash.slice(1);
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    },

    filterRange: function(e) {
        e.preventDefault();
        var from = $(this.el).find('input.from').val() || '';
        var to = $(this.el).find('input.to').val() || '';
        this.collection.date = from || to ? [from, to].join(',') : null;
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Topic filter view
 */
var TopicFilterView = FilterView.extend({
    events: {
        'click a': 'filter',
        'click em': 'remove'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('li').removeClass('main');

        for (var i = 0; i < this.collection.topics.length; i++) {
            $(this.el).find('a[href="#' + this.collection.topics[i] + '"]').closest('li').addClass('main');
        }

        if (this.collection.topics.length === 0) {
            $(this.el).find('li:first').addClass('main');
        }
    },

    filter: function(e) {
        e.preventDefault();
        var topic = e.target.hash.slice(1);
        if (topic != '') {
            this.collection.toggleTopic(topic);
        } else {
            this.collection.topics = [];
        }
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    },

    remove: function(e) {
        var elem = e.target;
        var id = parseInt($(elem).attr('id').split('-')[1]);
        var topics = {};
        topics[id] = 'false';
        $.post(this.options.url, {'topics': topics, 'format': 'json'}, function(data, textStatus, jqXHR) {
            $(elem).closest('li').detach();
        }, 'json');
    }
});

/**
 * Topic select view
 */
var TopicSelectView = FilterView.extend({
    events: {
        'change': 'filter'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('option:selected').removeAttr('selected');
        for (var i = 0; i < this.collection.topics.length; i++) {
            $(this.el).find('option[value="' + this.collection.topics[i] + '"]').prop('selected', true);
        }
    },

    filter: function(e) {
        if (e.target.value != '') {
            this.collection.toggleTopic(e.target.value);
        } else {
            this.collection.topics = [];
        }
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Pagination view
 */
var PaginationView = Backbone.View.extend({
    events: {
        'click .prev a': 'goTo',
        'click .next a': 'goTo'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).find('.start').text(Math.floor(this.collection.start / this.collection.rows) + 1);
        $(this.el).find('.end').text(Math.ceil(this.collection.count / this.collection.rows));

        if (this.collection.start > 0) { // active prev
        } else {
        }

        if (this.collection.count > this.collection.start + this.collection.rows) { // active next
        } else {
        }
    },

    goTo: function(e) {
        e.preventDefault();

        var lastStart = (Math.ceil(this.collection.count / this.collection.rows) - 1) * this.collection.rows;
        if ($(e.target).closest('li').hasClass('prev')) {
            var start = Math.max(0, this.collection.start - this.collection.rows);
        } else {
            var start = Math.min(lastStart, this.collection.start + this.collection.rows);
        }

        if (start == this.collection.start) {
            return;
        }

        this.collection.start = start;
        router.navigate(this.collection.nav(), {trigger: true});
        $(window).scrollTop($('.top-filter').offset().top);
    }
});

/**
 * Base ticker filter view
 */
var TickerFilterView = FilterView.extend({
    events: {
        'click a[href^=#]': 'filter'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    /**
     * Set main class to selected/first li
     *
     * @param {string} selected
     * @return {void}
     */
    setMain: function(selected) {
        $(this.el).find('li').removeClass('main');
        if (!selected) {
            $(this.el).find('li').first().addClass('main');
        } else {
            $(this.el).find('a[href="#' + selected + '"]').closest('li').addClass('main');
        }
    },

    /**
     * Set blacklisted options inactive
     *
     * @param {object} blacklist
     * @param {string} selected
     * @return {void}
     */
    setInactive: function(blacklist, selected) {
        var view = this;
        $(this.el).find('li').each(function() {
            var value = $(this).find('a').first().attr('href').slice(1);
            if (selected in blacklist && value in blacklist[selected]) {
                view.disableFilter($(this));
            } else {
                view.enableFilter($(this));
            }
        });
    },

    /**
     * Filter collection
     *
     * @param {object} e
     * @return {void}
     */
    filter: function(e) {
        e.preventDefault();
        var filter = $(e.target).attr('href').slice(1);
        this.collection[this.param] = filter ? filter : null;
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Source filter view
 */
var SourceFilterView = TickerFilterView.extend({
    blacklist: {
        basel: { twitter: true, en: true },
        schweiz: { twitter: true, en: true },
        international: { twitter: true, en: true },
        sport: { twitter: true, en: true },
        kultur: { twitter: true, en: true },
        leben: { twitter: true, en: true },
        blog: {
            twitter: true,
            agentur: true,
            link: true,
            en: true
        }
    },

    param: 'source',

    render: function() {
        this.setMain(this.collection.source);
        this.setInactive(this.blacklist, this.collection.section);
    }
});

/**
 * Section filter view
 */
var SectionFilterView = TickerFilterView.extend({
    blacklist: {
        twitter: {
            basel: true,
            schweiz: true,
            international: true,
            sport: true,
            kultur: true,
            leben: true,
            blog: true
        },
        agentur: {
            blog: true
        },
        link: {
            blog: true
        },
        en: {
            basel: true,
            schweiz: true,
            international: true,
            sport: true,
            kultur: true,
            leben: true,
            blog: true
        }
    },

    param: 'section',

    render: function() {
        this.setMain(this.collection.section);
        this.setInactive(this.blacklist, this.collection.source);
    }
});

/**
 * Sort view
 */
var SortView = Backbone.View.extend({
    events: {
        'click': 'sort'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        if (this.collection.sortf == 'latest') {
            $(this.el).find('a').removeClass('desc').addClass('asc');
        } else if (this.collection.sortf == 'oldest') {
            $(this.el).find('a').removeClass('asc').addClass('desc');
        } else {
            $(this.el).find('a').removeClass('desc').removeClass('asc');
        }

        $(this.el).css('cursor', 'pointer');
    },

    sort: function() {
        this.collection.sortf = this.collection.sortf == 'latest' ? 'oldest' : (this.collection.sortf == 'oldest' ? null : 'latest');
        this.collection.start = null;
        router.navigate(this.collection.nav(), {trigger: true});
    }
});

/**
 * Router
 */
var SearchRouter = Backbone.Router.extend({
    routes: {
        '?*params': 'search',
        '*params': 'search'
    },

    map: {
        q: 'query',
        type: 'type',
        date: 'date',
        start: 'start',
        sortf: 'sortf',
        source: 'source',
        section: 'section'
    },

    first: true,

    search: function(params) {
        if (history.pushState == undefined && window.documents.query == undefined) {
            this.setCollectionParams(window.documents, params);
        }

        window.documents.fetch();
    },

    setCollectionParams: function(collection, params) {
        var doubles = params.split('&');
        for (var i = 0; i < doubles.length; i++) {
            var param = doubles[i].split('=', 2);
            if (param[0] in this.map) {
                collection[this.map[param[0]]] = param[1].replace('/\+/g', ' ');
            } else if (param[0] == 'topic') {
                collection.topics = param[1].split(',');
            }
        }
    }
});
