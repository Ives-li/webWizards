const items = document.querySelectorAll(".navbar li");
console.log(items);
items.forEach((item) => {
    item.addEventListener("click", () =>{
        document.querySelector("li.active").classList.remove("active");
        item.classList.add("active");
    });
});