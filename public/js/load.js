window.onload = () =>{

    loadButton = document.getElementById("load");
    changeHref(loadButton)
    

    loadButton.addEventListener("click", function (event) {
      event.preventDefault();
      const el = this;

      axios.get(el.getAttribute('href')).then(function (response) {
        el.parentElement.insertAdjacentHTML('beforebegin', response.data);
        changeHref(el);
      });
    });
  
   
    function changeHref(el) {
      let parent = el.parentElement;
      let link = parent.previousElementSibling.innerHTML;
      if (link) {
        el.setAttribute('href', link);
      } else {
        parent.remove();
      }
    }
  
  }