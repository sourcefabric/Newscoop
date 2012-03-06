/**
 * Slideshow model
 */
window.Slideshow = Backbone.Model.extend({
    defaults: {
        attached: false
    },

    initialize: function() {
        if (window.article) {
            this.set('attached', _.indexOf(article.get('slideshows'), this.get('id')) !== -1);
        }
    },

    toggle: function() {
        var index = _.indexOf(article.get('slideshows'), this.get('id'));
        if (index === -1) {
            this.set('attached', true);
            article.get('slideshows').push(this.get('id'));
        } else {
            this.set('attached', false);
            article.get('slideshows').splice(index, 1);
        }
    }
});
