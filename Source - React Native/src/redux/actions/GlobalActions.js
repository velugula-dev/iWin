'use-strict'



export const TYPE_SAVE_DROPDOWN_INFO = "TYPE_SAVE_DROPDOWN_INFO"
export function saveDropDownInfoInRedux(details) {
    return {
        type: TYPE_SAVE_DROPDOWN_INFO,
        value: details
    }
}


export const TYPE_SAVE_APP_INFO = "TYPE_SAVE_APP_INFO"
export function saveAppInfoInfoInRedux(details) {
    return {
        type: TYPE_SAVE_APP_INFO,
        value: details
    }
}


