function deleteAnimal(tagId) {
    if(!confirm("Are you sure you want to delete this animal?")) return;

    fetch(serverURL, {
        method: "DELETE",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify({ tagId })
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) {
            alert(data.error);
        } else {
            alert("Animal deleted successfully!");
            loadAnimals();
        }
    });
}