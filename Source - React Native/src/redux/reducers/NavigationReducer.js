import { TYPE_SAVE_NAVIGATION_SELECTION } from "../actions/NavigationAction";
import { strings } from "../../locales/i18n";

const initalState = {
};

export function navigationOperation(state = initalState, action) {
  switch (action.type) {
    case TYPE_SAVE_NAVIGATION_SELECTION: {
      return Object.assign({}, state, {
        selectItem: action.value
      });
    }
    default:
      return state;
  }
}
