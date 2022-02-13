$(document).ready(function(){
    $(".content").slice(0, 10).show();
    $("#loadMore").on("click", function(e){
        e.preventDefault();
        $(".content:hidden").slice(0, 10).slideDown();
        if($(".content:hidden").length == 0) {
            $("#loadMore").addClass("d-none");
        }
    });

})