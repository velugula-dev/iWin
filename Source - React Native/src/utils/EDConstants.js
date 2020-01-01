import Moment from "moment";


// API URL CONSTANTS
let isLocalEnvironment = false;
// export const BASE_URL = isLocalEnvironment
//   ? "http://192.168.1.125/iwin/api/"
//   : "http://alltime.fit/iwin/v1/api/";

export const BASE_URL = "http://overunderz.com/v2/api/";
// export const BASE_URL = "http://alltime.fit/iwin/v2/api/";



// http://alltime.fit/iwin/v1/api

export const LOGIN_URL = BASE_URL + "login";
export const REGISTER_URL = BASE_URL + "registration";
export const FORGOT_PASSWORD_URL = BASE_URL + "forgotpassword";
export const GET_CMS_DATA = BASE_URL + "get_cms_data";
export const GET_QUESTIONS_URL = BASE_URL + "get_quiz_questions_list";
export const SUBMIT_ANSWERS_URL = BASE_URL + "save_user_answer";
export const GET_QUIZ_HISTORY_URL = BASE_URL + "get_user_quiz_history";
export const GET_QUIZ_RESULTS_URL = BASE_URL + "get_quiz_result";
export const LOGOUT_URL = BASE_URL + "logout";
export const GET_PROFILE_URL = BASE_URL + "get_profile_data";
export const SAVE_PROFILE_URL = BASE_URL + "save_profile_data";
export const GET_FEEDBACK_QUESTIONS_URL = BASE_URL + "feedback_msg_questions_list";
export const SAVE_FEEDBACK_ANSWERS_URL = BASE_URL + "save_user_feedback_answer";



export const API_PAGE_SIZE = 20;

// CLIENT ACCOUNT
export const AdMob_ANDROID_APP_ID = "ca-app-pub-1493723565821992~1710921742";
export const AdMob_ANDROID_AD_UNIT_ID_BANNER = "ca-app-pub-1493723565821992/9015353231";
export const AdMob_ANDROID_AD_UNIT_ID_INTERSTITIAL = "ca-app-pub-1493723565821992/8631553316";


export const AdMob_IOS_APP_ID = "ca-app-pub-1493723565821992~8860328610";
export const AdMob_IOS_AD_UNIT_ID_BANNER = "ca-app-pub-1493723565821992/2897555496";
export const AdMob_IOS_AD_UNIT_ID_INTERSTITIAL = "ca-app-pub-1493723565821992/7430152282";

export const AdMob_IOS_AD_UNIT_ID_INTERSTITIAL_TEST = "ca-app-pub-3940256099942544/4411468910";
export const AdMob_IOS_AD_UNIT_ID_BANNER_TEST = "ca-app-pub-3940256099942544/2934735716";


// EVINCE ACCOUNT
// export const AdMob_ANDROID_APP_ID = "ca-app-pub-9812043876886508~4236476333";
// export const AdMob_ANDROID_AD_UNIT_ID = "ca-app-pub-9812043876886508/8858247622";


// export const AdMob_IOS_APP_ID = "ca-app-pub-9812043876886508~7333412965";
// export const AdMob_IOS_AD_UNIT_ID = "ca-app-pub-9812043876886508/4240345762";


// ALERT CONSTANTS
export const APP_NAME = "OverUnderz";

export const DEFAULT_ALERT_TITLE = APP_NAME;

export const CMSPages = {
  privacyPolicy: "privacy-policy",
  TermsAndConditions: "terms-conditions",
  AppInfo: "app-info",
};

export const FeedbackQuestionType = {
  thumbsUpDown: "Thumbs-up-down",
  textInput: "Text-input",
};

export const FeedbackAnswerType = {
  thumbsUp: "Thumbs-up",
  thumbsDown: "Thumbs-down",
};


export const NotificationTypes = {
  questionsPosted: "questionsposted",
  answersPosted: "answersposted",
  winnersAnnouncement: "winnersannouncement",
  custom: "custom",
  feedback: "feedback"
};


export const AlertButtons = {
  ok: "Okay",
  cancel: "Cancel",
  notNow: "Not now",
  yes: "Yes",
  no: "No"
};

// REQUESTS CONSTANTS
export const RequestKeys = {
  contentType: "Content-Type",
  json: "application/json",
  authorization: "Authorization",
  bearer: "Bearer"
};


// STORAGE CONSTANTS
export const StorageKeys = {
  userDetails: "userDetails",
};

//QUESTIONS TYPE
export const QuestionType = {
  single: "SingleChoice",
  multi: "MultiChoice",
  yesNo: "YesNo"
}

//SIGN IN TYPE
export const SignInType = {
  email: "email",
  facebook: "FB",
  google: "GP"
}

// TEXT FIELD TYPES
export const TextFieldTypes = {
  email: "email",
  password: "password",
  phone: "phone",
  datePicker: "datePicker",
  default: "default",
  action: "action",
  picker: "picker",
  amount: "amount"

  // name: "name",
  // accountNumber:"accountNumber",
};

export function debugLog(a) {
  // for (var i = 0; i < arguments.length; i++) {
  //   console.log(arguments[i]);
  // }
}

export const funGetTomorrowDate = () => {
  var d = new Date();
  var newDate = Moment(d).add(1, "day");

  return new Date(newDate);
};

export const funGetDate = date => {
  var d = new Date(date);
  return Moment(d).format("HH:mm");
};

export function getDate(date) {
  console.log("check date", date);
  console.log("check date moment", new Date(Moment(date)));
  return new Date(date);
}

