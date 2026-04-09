registerForm.addEventListener("submit", (e) => {
    e.preventDefault();
    const formData = {
        tagId: document.getElementById("tagId").value,
        name: document.getElementById("name").value,
        animalType: document.getElementById("animalType").value,
        sex: document.getElementById("sex").value,
        breed: document.getElementById("breed").value,
        birthdate: document.getElementById("birthdate").value,
        ownerContact: document.getElementById("ownerContact").value
    };

    fetch(serverURL, { // serverURL now handles POST for new + PUT for update
        method: "POST",
        headers: {"Content-Type": "application/json"},
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if(data.error) {
            alert(data.error);
        } else {
            alert("Animal saved successfully!");
            loadAnimals();
            registerForm.reset();
            tagInput.value = "";
        }
    });
});
