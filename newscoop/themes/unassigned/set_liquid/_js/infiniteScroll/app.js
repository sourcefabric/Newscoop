

$(function() {





  window.lapp = {






  init : function(){



    this.view = new window.listView();

  },

  showMoreButton : function (){
    $("#load_more").css("display","block");
  },

  hideMoreButton : function (){
    $("#load_more").css("display","none");
  }

};

lapp.init();

});