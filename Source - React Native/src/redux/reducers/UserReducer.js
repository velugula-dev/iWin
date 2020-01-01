import {
  TYPE_SAVE_LOGIN_DETAILS,
  TYPE_REMOVE_LOGIN_DETAILS,
  TYPE_SAVE_APP_INFO_TEXT,
} from "../actions/User";

const initialStateUser = {
}



export function userOperations(state = initialStateUser, action) {
  switch (action.type) {
    case TYPE_SAVE_LOGIN_DETAILS: {
      return Object.assign({}, state, action.value);
    }
    case TYPE_REMOVE_LOGIN_DETAILS: {
      return Object.assign({}, state, {
        userId: undefined,
        sessionToken: undefined,
      })
    }



    default:
      return state
  }
}
