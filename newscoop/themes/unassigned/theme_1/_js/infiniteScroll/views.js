window.articleItemView = Backbone.View.extend({
    tagame : 'div',

    initialize: function() {

        if (this.model.isHighlighted()){
            this.$el.attr( "class", "span6 new" );

        }else{
            this.$el.attr( "class", "span3 new" );
        }
        this.$el.attr( "style", "display:none" );
    },

    render: function () {



            tpl = _.template($('#item-template').html());




        $(this.el).html(tpl({item: this.model}));




        return this;
    }


});

window.listView = Backbone.View.extend({
    el: "#masonry_container",




    initialize: function () {
        var self = this;



        this.collection = new window.articlesCollection();

        _.bindAll(this, 'render', 'afterRender');


        this.render = _.wrap(self.render, function(render) {
        render();
        self.afterRender();
        return self;
        });

        this.collection.nextPageLink = '/api/sections/'+section_number+'/'+lang+'/articles?sort[published]=desc';

            this.collection.fetch({reset: true}).complete(function(){



                self.render();


            });



        _.bindAll(this, 'loadMoreClickHandler');
        $("#load_more").unbind("click").bind("click", this.loadMoreClickHandler);





    },



    render: function () {
        console.log("render");
        var that = this;

        _.each(this.collection.models, function (item) {
            var itemView = new articleItemView({
                       model: item
                   });

                   that.$el.append(itemView.render().el);
        }, this);





    },

    afterRender: function () {
        that = this;


        var images = $('#masonry_container img.loading');
            var nimages = images.length;
            if (!nimages){
                $("div.new").fadeIn().removeClass("new");

                that.$el.masonry('reload');
                progressJs().end();
            }

            images.load(function() {
                nimages--;
                $(this).removeClass("loading");
                if(nimages === 0) {
                    $("div.new").fadeIn().removeClass("new");

                    that.$el.masonry('reload');
                    progressJs().end();
                }
            });



    },







    loadMoreClickHandler: function () {
        self = this;
        progressJs().start();
        progressJs().autoIncrease(4, 400);
            this.collection.fetch({reset: true}).complete(function(){



                self.render();


            });


    }




});