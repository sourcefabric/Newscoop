/**
 */
var User = Backbone.Model.extend({

    /**
     * Get link for given action
     *
     * @param {string} action
     * @return {string}
     */
    getLink: function(action) {
        for (var i = 0; i < this.get('links').length; i++) {
            if (this.get('links')[i].rel === action) {
                return this.get('links')[i].href;
            }
        }
    },

    /**
     * Get class name for user status
     *
     * @return {string}
     */
    getStatusClass: function() {
        switch (this.get('status')) {
            case 1:
                return 'active';
                break;

            case 3:
                return 'deleted';
                break;

            default:
                return 'inactive';
                break;
        }
    }
});

/**
 */
var UserCollection = Backbone.Collection.extend({
    model: User,

    parse: function(response) {
        this.pagination = response.pagination;
        this.criteria = response.criteria;
        return response.users;
    }
});

/**
 */
var UserView = Backbone.View.extend({
    tagName: 'li',

    events: {
        'click a.delete': 'delete',
        'click a.token': 'token'
    },

    initialize: function() {
        this.template = _.template($('#user-' + this.model.getStatusClass() + '-template').html());
    },

    render: function() {
        $(this.el).html(this.template({user: this.model})).addClass(this.model.getStatusClass());
        return this;
    },

    delete: function(e) {
        if (this.confirm(e.target)) {
            var model = this.model;
            $.getJSON(this.model.getLink('delete'), {'format': 'json'}, function(data, textStatus, jqXHR) {
                if (data.message) {
                    flashMessage(data.message, 'error');
                    return false;
                }

                model.collection.remove(model);
                flashMessage(window.translate['User was deleted.']);
            });
        }

        return false;
    },

    token: function(e) {
        if (this.confirm(e.target)) {
            $.getJSON(this.model.getLink('token'), {'format': 'json'}, function(data, textStatus, jqXHR) {
                flashMessage(window.translate['New confirmation email was sent to user.']);
            });
        }

        return false;
    },

    'confirm': function(target) {
        return confirm(window.translate['Are you sure you want to {action}?'].replace('{action}', $(target).attr('title')));
    }
});

/**
 */
var UserListView = Backbone.View.extend({
    initialize: function() {
        this.collection.bind('reset', this.render, this);
        this.collection.bind('remove', this.render, this);
    },

    render: function() {
        var list = $(this.el).empty();
        this.collection.each(function(user) {
            var view = new UserView({model: user});
            list.append(view.render().el);
        });

        if (this.collection.length === 0) {
            list.append($('<li />').text(window.translate['No users found.']));
        }

        return this;
    }
});

var StatusFilterView = Backbone.View.extend({
    activeClass: 'active',

    events: {
        'click a': 'filter'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        if ('status' in this.collection.criteria) {
            $(this.el).find('a[href="#' + this.collection.criteria.status + '"]').addClass(this.activeClass);
        }
    },

    filter: function(e) {
        e.preventDefault();
        var data = this.collection.criteria;
        data['status'] = e.currentTarget.hash.substring(1);
        this.collection.fetch({'data': data});
        $(this.el).find('a').removeClass(this.activeClass);
        $(e.currentTarget).addClass(this.activeClass);
    }
});

var PaginationView = Backbone.View.extend({
    events: {
        'click a': 'goto'
    },

    initialize: function() {
        this.collection.bind('reset', this.render, this);
    },

    render: function() {
        $(this.el).empty();

        var pagination = this.collection.pagination;
        if (pagination.prevPageOffset !== null) {
            $(this.el).append($('<a>&lt;</a>').attr('href', '#' + pagination.prevPageOffset));
        } else {
            $(this.el).append($('<span>&lt;</span>'));
        }

        if (pagination.nextPageOffset !== null) {
            $(this.el).append($('<a>&gt;</a>').attr('href', '#' + pagination.nextPageOffset));
        } else {
            $(this.el).append($('<span>&gt;</span>'));
        }

        return this;
    },

    'goto': function(e) {
        var data = this.collection.criteria;
        data['start'] = e.target.hash.substring(1);
        this.collection.fetch({'data': data});
    }
});

var SelectFilterView = Backbone.View.extend({
    events: {
        'change': 'filter'
    },

    filter: function(e) {
        console.log(e);
        var data = this.collection.criteria;
        data['groups'] = e.target.value;
        this.collection.fetch({'data': data});
    }
});

var SearchView = Backbone.View.extend({
    events: {
        'blur input': 'search',
        'change input': 'search'
    },

    'search': function(e) {
        var criteria = this.collection.criteria;
        criteria['q'] = $(this.el).find('input').val();
        this.collection.fetch({data: criteria});
    }
});
