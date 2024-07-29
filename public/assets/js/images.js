// let links = document.querySelectorAll("[data-delete]");

// // on boucle sur les liens
// for(let link of links){
//     // on met un écouter d'evenements
//     link.addEventListener('click', function(e){
//         // on empeche la navigation
//         e.preventDefault();

//         // on demande confirmation
//         if(confirm("Voulez-vous supprimer cette image ?")){
//             // on envoit la requête ajax
//             fetch(this.getAttribute("href"), {
//                 method: "DELETE",
//                 headers: {
//                     "X-Requested-With": "XMLHttpRequest",
//                     "Content-Type": "application/json"
//                 },
//                 body: JSON.stringify({"_token": this.dataset.token})
//             }).then(response => response.json())
//             .then(data => {
//                 if(data.success){
//                     const imageParentDiv = this.parentElement.parentElement;
//                     imageParentDiv.remove();
//                 } else {
//                     alert(data.error);
//                 }
//             })

//         }
//     })
// }