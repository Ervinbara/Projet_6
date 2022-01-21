
// const newItem = (e) => {
//   const collectionHolder = document.querySelector(e.currentTarget.dataset.collection)
//   console.log(collectionHolder);
//
//   // Le parse int permet de caster notre index en integer sinon ça concaténe
//   let index = parseInt(collectionHolder.dataset.index);
//   const prototype = collectionHolder.dataset.prototype;
//
//   collectionHolder.innerHTML += prototype.replace(/__name__/g, index);
//
//   collectionHolder.dataset.index = index + 1;
//
//   collectionHolder.querySelectorAll('.btn-remove')
//       .forEach(btn => btn.addEventListener("click",removeItem));
// };
//
// const removeItem = (e) => {
//   e.currentTarget.closest(".item").remove();
// };
//
//
// document.querySelectorAll('.btn-new')
//     .forEach(btn => btn.addEventListener("click",newItem));


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

  item.querySelector(".btn-remove").addEventListener("click", () => item.remove());

  collectionHolder.appendChild(item);

  collectionHolder.dataset.index++;
};

document
    .querySelectorAll('.btn-remove')
    .forEach(btn => btn.addEventListener("click", (e) => e.currentTarget.closest(".col-4").remove()));

document
    .querySelectorAll('.btn-new')
    .forEach(btn => btn.addEventListener("click", newItem));