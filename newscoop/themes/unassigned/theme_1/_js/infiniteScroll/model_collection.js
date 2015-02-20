$(function() {


  window.articleModel = Backbone.Model.extend({

    isHighlighted : function(){
      try
      {
        if (this.get('fields').highlight=="1") return true;
      }
      catch(err)
      {
        return false;
      }


      return false;
    },
    getDeck : function (){
      try
      {
        if (this.get('fields').deck) return this.get('fields').deck;
      }
      catch(err)
      {
        try
        {
          if (this.get('fields').teaser) return this.get('fields').teaser;
        }catch(error){
          return '';
        }

      }
      return '';
    },
    getRendition : function(name) {
      // to be updated in API
      var renditions = this.get('renditions');
      for (var i=0 ; i< renditions.length; i++){
        if(renditions[i].caption==name){
          return decodeURIComponent(renditions[i].link);
        }
      }
      return false;

    }
  });





  window.articlesCollection = Backbone.Collection.extend({
    model: articleModel,
    nextPageLink : '',




    url: function() {
      return this.nextPageLink;
    },

    parse: function(response) {

      if('pagination' in response){
        console.log(response.pagination.nextPageLink);
        if (response.pagination.nextPageLink !== undefined){
          this.nextPageLink = response.pagination.nextPageLink;
          window.lapp.showMoreButton();
        }else{
          window.lapp.hideMoreButton();
        }
      }


        var newList = _.filter(response.items,
          function(obj){

            if(obj.type != 'poll') return obj;

          });



        return newList;






    }





  });




});