
import { TYPE_SAVE_DROPDOWN_INFO, TYPE_SAVE_APP_INFO } from "../actions/GlobalActions";

const initialStateGeneral = {
}



export function generalOperations(state = initialStateGeneral, action) {
    switch (action.type) {
        case TYPE_SAVE_DROPDOWN_INFO: {
            return Object.assign({}, state, {
                dropdownDataInRedux: action.value,
            });
        }

        case TYPE_SAVE_APP_INFO: {
            return Object.assign({}, state, action.value);
        }

        default:
            return state
    }
}
