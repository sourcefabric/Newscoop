/**
 * Paginator view
 */
window.PaginatorView = Backbone.View.extend({
    events: {
        'click .next': 'loadNext',
        'click .prev': 'loadPrev',
        'click .first': 'loadFirst',
        'click .last': 'loadLast'
    },

    initialize: function() {
        this.pages = this.options.pages;
        this.collection = this.options.collection;

        this.page = 1;
        this.prev = false;
        this.next = this.page < this.pages;

        this.collection.bind('reset', this.render, this);

        this.template = _.template($('#paginator-template').html());
    },

    render: function() {
        $(this.el).html(this.template(this));
        return this;
    },

    loadNext: function(e) {
        e.preventDefault();

        if (this.page >= this.pages) {
            return;
        }

        this.page++;
        this.prev = true;
        this.next = this.page < this.pages;
        this.collection.fetch({data: {page: this.page}});
    },

    loadPrev: function(e) {
        e.preventDefault();

        if (this.page < 1) {
            return;
        }

        this.page--;
        this.prev = this.page > 1;
        this.next = true;
        this.collection.fetch({data: {page: this.page}});
    },

    loadFirst: function(e) {
        this.page = 2;
        this.loadPrev(e);
    },

    loadLast: function(e) {
        this.page = this.pages - 1;
        this.loadNext(e);
    }
});
