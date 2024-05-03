"use strict";

/**
 * This function checks that an email address is valid. It is correct, don't mess with it. It is
 * identical to the function from last week's lab, but is written less verbosely.
 *
 * This function uses regular expressions, like those used with the `preg_*` family of functions in
 * PHP. @see https://regex101.com/r/5w9EYJ/1
 */
const checkEmail = str => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(str);

const createAccount = document.querySelector("#create_account");

if (createAccount) {
  // "Global" variable that we will update in the new event listener and check in the original
  // submit listener
  let emailIsValid = false;
  let userIsValid = false;

  // Select all of the elements we'll need in order to perform validation.
  const emailInput = document.querySelector("#email");
  const emailError = emailInput.parentElement.nextElementSibling;

  const usernameInput = document.querySelector("#username");
  const usernameError = usernameInput.parentElement.nextElementSibling;

  const nameInput = document.querySelector("#name");
  const nameError = nameInput.parentElement.nextElementSibling;

  const passwordInput = document.querySelector("#password");
  const confirmpasswordInput = document.querySelector("#password_confirm");
  const passwordError = passwordInput.parentElement.nextElementSibling;


  // Add an event listener to the form that will use those selected elements to validate the inputs
  // and display the error messages.
  createAccount.addEventListener("submit", (ev) => {
    let errors = false;
    
    // Check if name is valid and handled appropriately
    if (!nameInput.value) {
        nameError.classList.remove("hidden");
        errors = true;
    } else if ((nameInput.value).indexOf(" ") == -1) {
        nameError.classList.remove("hidden");
        errors = true;
    } else {
        nameError.classList.add("hidden");
    }
    
    
    // Check if password is valid and handled appropriately
    if ((!(passwordInput.value && confirmpasswordInput.value))) {
        passwordError.classList.remove("hidden");
        errors = true;
    } else if (!(passwordInput.value == confirmpasswordInput.value)) {
        passwordError.classList.remove("hidden");
        errors = true;
    }else {
        passwordError.classList.add("hidden");
    }
    

    // IF THERE ARE ERRORS, PREVENT FORM SUBMISSION
    if (errors || !emailIsValid || !userIsValid) {
      ev.preventDefault();
    }
    
  });

  // Validate Email
  emailInput.addEventListener("blur", (ev) => {
    const email = document.querySelector('#error');

    if(email) {
      email.remove();
    }
    //instantiate new XHR object
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "checkemail.php?email="+(emailInput.value));
    xhr.addEventListener("load", (ev) => {
      if (xhr.status == 200) {
        //success
        const position = document.querySelector("#emailInput");
        if (xhr.responseText == 'error') {
        emailIsValid = false;
        position.insertAdjacentHTML("afterend", "<span class='error' id='error'>Please use a valid email address.</span>" );
        } else if (xhr.responseText == 'true') {
          emailIsValid = false;
          position.insertAdjacentHTML("afterend", "<span class='error' id='error'>Email already exists</span>" );
        } else if (xhr.responseText == 'false') {
          emailIsValid = true;
        }
      }
    });
    xhr.send();
    });

  // Validate Username
  usernameInput.addEventListener("blur", (ev) => {
    const username = document.querySelector('#error');    
    if(username) {
        username.remove();
    }
    //instantiate new XHR object
    const xhr = new XMLHttpRequest();
    xhr.open("GET", "checkuser.php?username="+(usernameInput.value));
    xhr.addEventListener("load", (ev) => {
        if (xhr.status == 200) {
          //success
          const position = document.querySelector("#usernameInput");
          if (xhr.responseText == 'error') {
            userIsValid = false;
            position.insertAdjacentHTML("afterend", "<span class='error' id='error'>Please enter a username.</span>" );
          } else if (xhr.responseText == 'true') {
            userIsValid = false;
            position.insertAdjacentHTML("afterend", "<span class='error' id='error'>User already exists.</span>" );
          } else if (xhr.responseText == 'false') {
            userIsValid = true;
          }
        }
    });
    xhr.send();
    });
}

const createSheet = document.querySelector("#create_sheet");

if (createSheet) {
  // Select all of the elements we'll need in order to perform validation.
  const titleInput = document.querySelector("#sheet_title");
  const titleError = titleInput.parentElement.nextElementSibling;

  const descriptionInput = document.querySelector("#sheet_description");
  const descriptionError = descriptionInput.parentElement.nextElementSibling;


  // Add an event listener to the form that will use those selected elements to validate the inputs
  // and display the error messages.
  createSheet.addEventListener("submit", (ev) => {
    let errors = false;
    // Check if name is valid and handled appropriately
    if (!titleInput.value) {
        titleError.classList.remove("hidden");
        errors = true;
    } else if (length(titleInput.value) === 0) {
        titleError.classList.remove("hidden");
        errors = true;
    } else {
        titleError.classList.add("hidden");
    }
    // Check if name is valid and handled appropriately
    if (!descriptionInput.value) {
      descriptionError.classList.remove("hidden");
      errors = true;
  } else if (length(descriptionInput.value) === 0) {
      descriptionError.classList.remove("hidden");
      errors = true;
  } else {
      descriptionError.classList.add("hidden");
  }
  // IF THERE ARE ERRORS, PREVENT FORM SUBMISSION
  if (errors) {
    ev.preventDefault();
  }
  });
}


var url_string = window.location.href;
var url = new URL(url_string);
var id = url.searchParams.get("id");

if (url == 'https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn4/slotdetails.php?id='+id) {
  // Confirm Cancel Signup
  const confirmcancel = document.getElementById("cancel");
  function confirm_cancel() {
    if (confirm("Are you sure you want to cancel? This cannot be undone.")) {
      window.location.replace('cancel.php?id='+id);
    } else {
      window.location.href='index.php';
    }
  }
  confirmcancel.addEventListener("click", confirm_cancel);
}

if (url == 'https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn4/sheetdetails.php?id='+id) {
  // Confirm Delete Sheet
  const confirmdelete = document.getElementById("delete");
  function confirm_delete() {
    if (confirm("Are you sure you want to delete this sheet? This cannot be undone.")) {
      window.location.replace('deletesheet.php?id='+id);
    } else {
      window.location.href='index.php';
    }
  }
  confirmdelete.addEventListener("click", confirm_delete); 
}

if (url == 'https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn4/index.php') {
  // Confirm Delete Account
  const deleteaccount = document.getElementById("delete_account");
  function delete_account() {
    if (confirm("Are you sure you want to delete your account? This cannot be undone.")) {
      window.location.replace('deleteaccount.php');
    } else {
      window.location.href='index.php';
    }
  }
  deleteaccount.addEventListener("click", delete_account)
}

if (url == 'https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn4/register.php') {
  var security = document.getElementById("password_strength");
  var input = document.getElementById("password");
  if (input) {
      input.addEventListener("keyup", function() {
      checkpassword(input.value);
  });
  }
  function checkpassword(password) {
      var strength = 0;
      // Password Character Strength
      if (password.match(/[a-z]+/)) {
          strength += 1;
      } if (password.match(/[A-Z]+/)) {
          strength += 1;
      } if (password.match(/[0-9]+/)) {
          strength += 1;
      } if (password.match(/[$@#&!]+/)) {
          strength += 1;
      }
      // Password Length Strength
      if (password.length >= 20) {
          strength += 1;
      } if (password.length >= 10) {
          strength += 1;
      } if (password.length >= 5) {
          strength += 1;
      }
      // Password Strength Calculate
      switch (strength) {
          case 0:
          security.value = 0;
          break;

          case 1:
          security.value = 14;
          break;

          case 2:
          security.value = 28;
          break;

          case 3:
          security.value = 42;
          break;

          case 4:
          security.value = 56;
          break;

          case 5:
          security.value = 70;
          break;

          case 6:
          security.value = 84;
          break;

          case 7:
          security.value = 100;
          break;
      }
  }
}

if (url == 'https://loki.trentu.ca/~ryanvanwolde/3420/assn/assn4/index.php') {
  // Confirm Delete Sheet
  const confirmdelete = document.getElementById("delete");
  function confirm_delete() {
    if (confirm("Are you sure you want to delete this sheet? This cannot be undone.")) {
      window.location.replace('deletesheet.php?id='+id);
    } else {
      window.location.href='index.php';
    }
  }
  confirmdelete.addEventListener("click", confirm_delete); 
}