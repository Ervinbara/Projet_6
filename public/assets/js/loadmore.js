$(document).ready(function(){
    self = $('#tricks');
    btn = $('#loadMore');

    $("#loadMore").on("click", function(e) {
        e.preventDefault();
        // Si il n'y a plus de contenu à charger, on change le texte du bouton "Voir plus" en "No content"
        if (parseInt(document.getElementById("loadMore").dataset.offset) >= parseInt(document.getElementById("loadMore").dataset.limit)){
            console.log("arrertttetee");
            document.getElementById("loadMore").text = "No content";
            return 0;
        }
        $.ajax({
            type:"GET",
            url:`http://localhost:8000/${parseInt(this.dataset.offset)}`,

            success: function(result){
                let increment = parseInt(document.getElementById("loadMore").dataset.offset);
                self.append(result);
                // console.log("more ",increment);
                setTimeout(function(){
                    self.find('.help-modal').addClass('animate');
                        // Incrémenter de 8 la veleur de data-offset
                        this.id += 8;
                        // console.log(this);
                        document.getElementById("loadMore").dataset.offset = `${increment + 8}`;
                }, 100);
            }});
    });
})