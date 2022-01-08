window.onload = () => {

    
    var links = document.querySelectorAll("[data-delete]");
    
    for(let link of links){
        
        
        link.addEventListener("click", function(e){
            e.preventDefault();
            let media = e.currentTarget;
            deleteMedia(media);
    
    });
    }
    document
    .querySelectorAll(".btn-remove")
    .forEach(btn => btn.addEventListener("click", (e) => e.currentTarget.closest(".col-8").remove()));
  
  document
    .querySelectorAll(".btn-new")
    .forEach(btn => btn.addEventListener("click", newItem));
}


// delete Pictures
function deleteMedia(media){
        // on demande confirmation
        if(confirm("Do you want to delete this image?")){
            
            // on envoi une requete Ajax vers le href du lien avec la methode DELETE
            fetch(media.getAttribute("href"),{
                method : "DELETE",
                headers: {
                    "X-Requested-With" : "XMLHttpRequest",
                    "Content-Type" : "application/json" 
                },
                body: JSON.stringify({"_token" : media.dataset.token})
            }).then(
                // on recupere la reponse en JSON
                (response) => response.json()
            ).then((data) => {
                if(data.success){
                    media.parentElement.remove();
                }else{
                    alert(data.error);
                }
            }).catch((e) => alert(e));
        }
}

// add new url form
const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);
  
    const item = document.createElement("div");
    item.classList.add("col-8");
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
  
 