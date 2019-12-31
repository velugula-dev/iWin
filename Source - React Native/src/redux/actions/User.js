'use-strict'



export const TYPE_SAVE_LOGIN_DETAILS = "TYPE_SAVE_LOGIN_DETAILS"
export function saveUserDetailsOnLogin(details) {
  return {
    type: TYPE_SAVE_LOGIN_DETAILS,
    value: details
  }
}

export const TYPE_REMOVE_LOGIN_DETAILS = "TYPE_REMOVE_LOGIN_DETAILS"
export function removeUserDetailsOnLogout() {
  return {
    type: TYPE_REMOVE_LOGIN_DETAILS
  }
}

