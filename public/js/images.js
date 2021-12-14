window.onload = () =>{
    
    // bouton supprimer
    var links = document.querySelectorAll("[data-delete]");

    // on booucle sur les boutons
    for(link of links){
        
        // on ecoute le click
        link.addEventListener('click', function(e){
            e.preventDefault()
            
        // on demande confirmation
        if(confirm("Voulez-vous supprimer cette image ?")){
            
            // on envoi une requete Ajax vers le href du lien avec la methode DELETE
            fetch(this.getAttribute("href"),{
                method : "DELETE",
                headers: {
                    "X-Requested-With" : "XMLHttpRequest",
                    "Content-Type" : "application/json" 
                },
                body: JSON.stringify({"_token" : this.dataset.token})
            }).then(
                // on recupere la reponse en JSON
                response => response.json()
            ).then(data => {
                if(data.success){
                    this.parentElement.remove()
                }else{
                    alert(data.error)
                }
            }).catch(e => alert(e))
        }
    })
    }

}