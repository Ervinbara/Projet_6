// $(document).ready(function(){
//     $(".card").slice(0, 8).show();
//     $("#loadMore").on("click", function(e){
//       e.preventDefault();
//       $(".card:hidden").slice(0, 5).slideDown();
//       if($(".card:hidden").length == 0) {
//         $("#loadMore").text("No content").addClass("noContent");
//       }
//     });
//
//   })


$(document).ready(function(){
    self = $('#tricks');
    btn = $('#loadMore')
    $("#loadMore").on("click", function(e) {
        e.preventDefault();
        $.ajax({
            type:"GET",
            url:"http://localhost:8000/20",
            // Ici dire si y'a R à charger, modifier le load more "no content" ou "pas de contenu disponible"
            // error: function(btn){
            //     console.log("pas de content poto")
            // },

            success: function(result){
                self.append(result);
                setTimeout(function(){
                    self.find('.help-modal').addClass('animate');
                    document.getElementById("result_no").value = Number(val)+2;
                }, 100);
            }});
    });
})


// function initLoad() {
//     self = $('#content');
//     $('#btn').click(function() {
