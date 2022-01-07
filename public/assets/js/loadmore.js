$(document).ready(function(){
    $(".card").slice(0, 4).show();
    $("#loadMore").on("click", function(e){
      e.preventDefault();
      $(".card:hidden").slice(0, 4).slideDown();
      if($(".card:hidden").length == 0) {
        $("#loadMore").text("No content").addClass("noContent");
      }
    });
    
  })