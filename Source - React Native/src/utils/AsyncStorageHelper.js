import AsyncStorage from "@react-native-community/async-storage"
import { StorageKeys, debugLog } from "./EDConstants";

export function flushAllData(onSuccess, onFailure) {
  AsyncStorage.clear().then(
    success => onSuccess(success),
    err => onFailure(err)
  );
}

export function saveUserLoginDetails(details, onSuccess, onFailure) {

  AsyncStorage.setItem(
    StorageKeys.userDetails,
    JSON.stringify(details)
  ).then(success => onSuccess(success), err => onFailure(err));
}

export function getUserLoginDetails(onSuccess, onFailure) {
  AsyncStorage.getItem(StorageKeys.userDetails).then(
    res => {
      console.log("Response", res);
      if (res != "" && res != null && res != undefined) {
        onSuccess(JSON.parse(res));
      } else {
        onFailure("Token Null");
      }
    },
    err => onFailure(err)
  );
}

export function getValueFromAsyncStore(keyToFetch, onSuccess, onFailure) {
  AsyncStorage.getItem(keyToFetch).then(
    res => {
      console.log("Response", res);
      if (res != "" && res != null && res != undefined) {
        onSuccess((res));
      } else {
        debugLog("Can not find value for ::: " + keyToFetch)
        if(keyToFetch == "fcmToken") {
          onSuccess(("123"));
        }else {
          onFailure("Could not find value");
        }
      }
    },
    err => onFailure(err)
  );
}
