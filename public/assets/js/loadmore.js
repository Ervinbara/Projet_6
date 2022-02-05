$(document).ready(function () {
    self = $('#tricks');
    btn = $('#loadMore');

    $("#loadMore").on("click", function (e) {
        e.preventDefault();
        $.ajax({
            type: "GET",
            url: `http://localhost:8000/${parseInt(this.dataset.offset)}`,

            success: function (result) {
                let increment = parseInt(document.getElementById("loadMore").dataset.offset);
                self.append(result);
                // console.log("more ",increment);
                setTimeout(function () {
                    self.find('.help-modal').addClass('animate');
                    // Incrémenter de 8 la veleur de data-offset
                    // this.id += 8;
                    document.getElementById("loadMore").dataset.offset = `${increment + 8}`;
                    // Si il n'y a plus de contenu à charger, on change le texte du bouton "Voir plus" en "No content"
                    if (parseInt(document.getElementById("loadMore").dataset.offset) >= parseInt(document.getElementById("loadMore").dataset.limit)) {
                        btn.addClass('d-none');
                        return 0;
                    }
                }, 100);
            }
        });
    });
})