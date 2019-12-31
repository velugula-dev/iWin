import { Alert, BackHandler } from "react-native";
import { AlertButtons, DEFAULT_ALERT_TITLE, APP_NAME } from "./EDConstants";
import { Messages } from "./Messages";
import { strings } from "../locales/i18n";

export function showDialogue(
  message,
  arrayButtons,
  title = "",
  okButtonHandler = () => { }
) {
  arrayButtonsToShow = (arrayButtons || []).concat([
    { text: AlertButtons.ok, onPress: okButtonHandler }
  ]);

  Alert.alert(title, message, arrayButtonsToShow, { cancelable: false });
}

export function showLogoutAlertWithCompletion(onYesClick) {
  Alert.alert(
    "",
    Messages.logoutConfirmation,
    [
      { text: AlertButtons.no },
      { text: AlertButtons.yes, onPress: () => onYesClick() }
    ],
    { cancelable: false }
  );
}

export function showRemovePhotoConfirmationAlertWithCompletion(onYesClick) {
  Alert.alert(
    Messages.removePhotoConfirmation,
    "",
    [
      { text: AlertButtons.no },
      { text: AlertButtons.yes, onPress: () => onYesClick() }
    ],
    { cancelable: false }
  );
}

export function showNoInternetAlert() {
  arrayButtonsToShow = [{ text: AlertButtons.ok }];
  Alert.alert(
    "",
    Messages.noInternet,
    arrayButtonsToShow,
    { cancelable: false }
  );
}

export function showNotImplementedAlert() {
  arrayButtonsToShow = [{ text: AlertButtons.ok }];
  Alert.alert(
    Messages.notImplementedTitle,
    Messages.notImplementedMessage,
    arrayButtonsToShow,
    { cancelable: false }
  );
}

export function showRNUpdateAlert() {
  arrayButtonsToShow = [{ text: AlertButtons.ok }];
  Alert.alert(
    APP_NAME,
    Messages.rnUpdateMessage,
    arrayButtonsToShow,
    { cancelable: false }
  );
}

export function exitAlert() {
  Alert.alert("Confirm exit", Messages.exitApp, [
    { text: "CANCEL", style: "cancel" },
    {
      text: "OK",
      onPress: () => {
        BackHandler.exitApp();
      }
    }
  ]);
}

export function notificationAlert(title, body, viewButtonHandler) {
  Alert.alert(title, body, [
    { text: strings("General.ok"), onPress: viewButtonHandler }
  ]);
}
