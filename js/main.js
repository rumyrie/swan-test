//the root for REST services
const rootURL = "http://localhost/books/";

var currentBook = null;
var currentButton = null;
var list = [];

class Book {

    constructor(id, name, author, genre, date) {
        this.id = id;
        this.name = name;
        this.author = author;
        this.genre = genre;
        this.date = date;
    }

    jsonSerialize() {
        var obj = {
            "id": this.id,
            "name": this.name,
            "author": this.author,
            "genre": this.genre,
            "publication_date": this.date
        };
        return JSON.stringify(obj);
    }

    constructFromJson(jsonBook) {
        var raw = JSON.parse(jsonBook);
        this.constructor(
            raw["id"],
            raw["name"],
            raw["author"],
            raw["genre"],
            raw["date"]
        );
    }
}

var buttons = {
    "ok": document.getElementById("btnOK"),
    "add": document.getElementById("btnAddBook"),
    "del": document.getElementById("btnDeleteBook"),
    "edit": document.getElementById("btnEditBook")
};

var input = {
    "id": document.getElementById("bookId").value,
    "name": document.getElementById("name").value,
    "author": document.getElementById("author").value,
    "genre": document.getElementById("genre").value,
    "publication_date": document.getElementById("publication_date").value
};

function hideButtons() {
    buttons.forEach(function (button, i) {
        if (i !== "add")
            button.style.visibility = "hidden";
    });
}

hideButtons();
renderList();

function getList() {
    var xhr = new XMLHttpRequest();
    xhr.open("GET", rootURL + "list", true);
    xhr.send();

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            return JSON.parse(xhr.responseText);
        } else {
            alert('Request failed. Returned status of' + xhr.status);
        }
    };
}

function createList() {
    var rawBooks = getList();
    rawBooks.forEach(function (raw) {
        list[raw["id"]] = new Book(
            raw["id"],
            raw["name"],
            raw["author"],
            raw["genre"],
            raw["date"]
        );
    });
}

function renderList() {
    createList();

    list.forEach(function (book) {
        renderElement(book);
    });
}

function renderElement(book) {
    var li = document.createElement("li");
    li.setAttribute("id", book.id);
    li.innerHTML = book.name;
    document.getElementById("booksList").appendChild(li);
}

function addBook(book) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", rootURL + "add", true);
    xhr.setRequestHeader("Content-Type", "application/json");
    xhr.send(book.jsonSerialize());

    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            var book = new Book().constructFromJson(xhr.responseText);
            list[book.id] = book;
            renderElement(book);
        }
    }
}

function deleteBook(currentBook) {
    var xhr = new XMLHttpRequest();
    xhr.open("POST", rootURL + currentBook + "/delete", true);
    xhr.send();
    xhr.onreadystatechange = function () {
        if (xhr.readyState === XMLHttpRequest.DONE && xhr.status === 200) {
            deleteElement(currentBook);
        }
    }
}

function deleteElement(currentBook) {
    var el = document.getElementById(currentBook);
    document.getElementById("booksList").removeChild(el);

    list = list.slice(currentBook, 1);
}

buttons["ok"].onclick(function () {
    var book = new Book(
        input["id"],
        input["name"],
        input["author"],
        input["genre"],
        input["date"]
    );
    if (currentButton === "del")
        deleteBook(book);
    else if (currentButton === "edit")
        editBook(book);
    else if (currentButton === "add")
        addBook(book);

});

buttons["add"].onclick(function () {
    currentButton = "add";
    buttons["ok"].style.visibility = "visible";
});

buttons["del"].onclick(function () {
    currentButton = "del";
    buttons["ok"].style.visibility = "visible";
});

buttons["edit"].onclick(function () {
    currentButton = "edit";
    buttons["ok"].style.visibility = "visible";
});