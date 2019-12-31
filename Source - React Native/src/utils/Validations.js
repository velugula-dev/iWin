import { Messages } from "../utils/Messages";

export default class Validations {
  isUrl(strToCheck) {
    var regexp = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
    return regexp.test(strToCheck);
  }
  // This function is used for checking type od EDTextField and accordingly secures the text entry
  checkingFieldType = fieldType => {
    if (fieldType === "password") {
      return true;
    } else {
      return false;
    }
  };

  // checkForEmpty = text => {
  //   if (text.length == 0) {
  //     return {
  //       isEmpty: true,
  //       validationErrorMessage: "asdasdasdasasdas"
  //     };
  //   }
  // }

  // Function for performing email validations
  validateEmail = (text, message = "This is a required field") => {
    // console.log(text);
    let reg = /^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/;
    // console.log("Regular Expression test is " + reg.test(text));
    if (text === "") {
      return message;
    } else if (reg.test(text) === false) {
      return Messages.validEmail;
    } else {
      return "";
    }
  };

  // Function for performing Password validations
  validatePassword = (text, message = "This is a required field") => {
    // console.log(text);
    // let reg = /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/;
    let reg = /^.{6,}/;


    // console.log("Regular Expression test is " + reg.test(text));
    if (text === "") {
      return message;
    } else if (reg.test(text) === false) {
      return Messages.validPassword;
    } else {
      return "";
    }
  };

  validateName = (text, message = "This is a required field") => {
    // console.log(text);
    // let reg = /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/;
    // let reg = /^[\w'\-,.][^0-9_!¡?÷?¿/\\+=@#$%ˆ&*(){}|~<>;:[\]]{2,}$/
    // let reg = /^([a-zA-Z0-9])$/;
    let reg = /^[a-zA-Z0-9\\s]*$/;
    if (text === "") {
      return message;
    } else if (reg.test(text) === false) {
      return message;
    } else {
      return "";
    }
  };

  validateEmpty = (text, message = "This is a required field") => {
    if (text.trim() == "") {
      return message;
    }
    return ""
  };
}
