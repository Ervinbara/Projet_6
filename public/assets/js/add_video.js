const newItem = (e) => {
  const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

  const item = document.createElement("div");
  item.classList.add("col-4");
  item.innerHTML = collectionHolder
      .dataset
      .prototype
      .replace(
          /__name__/g,
          collectionHolder.dataset.index
      );

  item.querySelector(".btn-remove").addEventListener("click", (e) => deleteVideo(e));

  collectionHolder.appendChild(item);

  collectionHolder.dataset.index++;
};

document
    .querySelectorAll('.btn-remove')
    .forEach(btn => btn.addEventListener("click", (e) =>{
        deleteVideo(e);
    } ));

document
    .querySelectorAll('.btn-new')
    .forEach(btn => btn.addEventListener("click", newItem));

function deleteVideo(e){
    if(confirm("Êtes vous sur de vouloirs supprimer cet élément ?")){
        e.currentTarget.closest(".col-4").remove();
    }
}