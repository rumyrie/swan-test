
//region Global variable declaration
const rootURL = "http://localhost:8080/books/";
let currentButton = null;
let list = {};

const buttons = {
    "ok": document.getElementById("btnOK"),
    "add": document.getElementById("btnAddBook"),
    "del": document.getElementById("btnDeleteBook"),
    "edit": document.getElementById("btnEditBook"),
    "get": document.getElementById("btnGetList")
};

const input = {
    "id": document.getElementById("bookId"),
    "name": document.getElementById("name"),
    "author": document.getElementById("author"),
    "genre": document.getElementById("genre"),
    "publication_date": document.getElementById("publication_date")
};
//endregion

class Book {

    constructor(id, name, author, genre, date) {
        this.id = id;
        this.name = name;
        this.author = author;
        this.genre = genre;
        this.date = date;
    }

    jsonSerialize() {
        const obj = {
            "id": this.id,
            "name": this.name,
            "author": this.author,
            "genre": this.genre,
            "publication_date": this.date
        };
        return JSON.stringify(obj);
    }
}

switchVisibilityButtons("hidden");
renderList();

//region async Get List Code
function asyncGet() {
    return new Promise(function (success, fail) {
        let xhr = new XMLHttpRequest();
        xhr.open("GET", rootURL + "list", true);
        xhr.onload = () => {
            if (xhr.status === 200)
                success(JSON.parse(xhr.responseText));
            else fail(alert(xhr.statusText));
        };
        xhr.onerror = () => fail(alert(xhr.statusText));
        xhr.send();
    });
}

async function createList() {
    let rawBooks = await asyncGet().then(
        function (data) {
            return data;
        }
    );
    if (rawBooks.length > 0)
        rawBooks.forEach(function (raw) {
            list[raw["id"]] = new Book(
                raw["id"],
                raw["name"],
                raw["author"],
                raw["genre"],
                raw["publication_date"]
            );
        });
}

async function renderList() {
    await createList();

    for (var key in list) {
        renderElement(list[key]);
    }
}
//endregion

//region async Add Code
function asyncAdd(book) {
    return new Promise(function (success, fail) {
        let xhr = new XMLHttpRequest();
        xhr.open("POST", rootURL + "add", true);
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onload = () => {
            if (xhr.status === 200)
                success(JSON.parse(xhr.responseText));
        };
        xhr.onerror = () => fail(alert(xhr.statusText));
        xhr.send(book.jsonSerialize());
    });
}

async function renderAdd(book) {
    let raw = await asyncAdd(book);
    let newBook = new Book(
        raw["id"],
        raw["name"],
        raw["author"],
        raw["genre"],
        raw["publication_date"]
    );
    renderElement(newBook);
}

//endregion

//region async Delete Code
function asyncDel(book) {
    return new Promise(function (success, fail) {
        let xhr = new XMLHttpRequest();
        xhr.open("DELETE", rootURL + book.id + "/delete");
        xhr.onload = () => {
            if (xhr.status === 200)
                success(true);
        };
        xhr.onerror = () => fail(alert(xhr.statusText));
        xhr.send();
    })
}

async function deleteBook(currentBook) {
    let check = await asyncDel(currentBook);

    if (check) {
        deleteElement(currentBook.id);
    }
}


function deleteElement(curBookId) {
    let el = document.getElementById(curBookId);
    document.getElementById("booksList").removeChild(el);
    remakeList(curBookId);
    clearInput();
}

function remakeList(curBookId) {
    let newList = {};
    for (var key in list) {
        if (key !== curBookId) {
            newList[key] = list[key];
        }
    }
    list = newList;
}

//endregion

//region async Edit Code
function asyncEdit(book) {
    return new Promise(function (success, fail) {
        let xhr = new XMLHttpRequest();
        xhr.open("PUT", rootURL + book.id + "/edit");
        xhr.setRequestHeader("Content-Type", "application/json");
        xhr.onload = () => {
            if (xhr.status === 200)
                success(JSON.parse(xhr.responseText));
        };
        xhr.onerror = () => fail(alert(xhr.statusText));
        xhr.send(book.jsonSerialize());
    });
}

async function editBook(book) {
    let editedBook = await asyncEdit(book);
    list[editedBook.id] = editedBook;
    reRenderElement(editedBook);
}

function reRenderElement(book) {
    let li = document.getElementById(book.id);
    li.innerText = book.id + ': ' + book.name;
}

//endregion

//region helper functions
function renderElement(book) {
    let li = document.createElement("li");
    li.setAttribute("id", book.id);
    li.innerText = book.id + ': ' + book.name;

    li.onclick = function () {
        switchVisibilityButtons("visible");
        currentButton = this.id;
        setInput(book);
    };

    document.getElementById("booksList").appendChild(li);
}

function clearList() {
    let bookList = document.getElementById("booksList");
    while (bookList.firstChild) {
        bookList.removeChild(bookList.firstChild);
    }
    list = [];

    clearInput();
}

function setInput(book) {
    input["id"].value = book.id;
    input["name"].value = book.name;
    input["author"].value = book.author;
    input["genre"].value = book.genre;
    input["publication_date"].value = book.date;
}

function clearInput() {
    for (var key in input) {
        input[key].value = "";
    }
}

function inputIsEmpty() {
    for (var key in input) {
        if ("".localeCompare(input[key].value) === 0)
            if (key.localeCompare("id") !== 0)
                return true;
    }
    return false;
}

function validate() {
    let date = input["publication_date"].value;
    const regex = /^(\d{4})-(0[1-9]|1[0-2])-(0[1-9]|[12][0-9]|3[01])$/;
    return regex.test(date) ? true : alert('wrong date format');
}

function switchVisibilityButtons(visibility) {
    for (var key in buttons)
        if (key.localeCompare("get") !== 0 && key.localeCompare("add") !== 0)
            buttons[key].style.visibility = visibility;
}

//endregion

//region main buttons onclick behavior
buttons["ok"].onclick = function () {
    if (!inputIsEmpty()) {
        const book = new Book(
            input["id"].value,
            input["name"].value,
            input["author"].value,
            input["genre"].value,
            input["publication_date"].value
        );
        if (currentButton === "del")
            deleteBook(book);
        else if (currentButton === "edit") {
            if (validate())
                editBook(book);
        }
        else if (currentButton === "add") {
            if (validate())
                renderAdd(book);
        }
    } else {
        alert('Some or all of input fields are empty');
    }
};

buttons["add"].onclick = function () {
    currentButton = "add";
    clearInput();
    switchVisibilityButtons("hidden");
    buttons["ok"].style.visibility = "visible";
};

buttons["del"].onclick = function () {
    currentButton = "del";
    buttons["ok"].style.visibility = "visible";
};

buttons["edit"].onclick = function () {
    currentButton = "edit";
    buttons["ok"].style.visibility = "visible";
};

buttons["get"].onclick = function () {
    currentButton = null;
    list = [];
    switchVisibilityButtons("hidden");
    clearList();
    renderList();
};
//endregion
