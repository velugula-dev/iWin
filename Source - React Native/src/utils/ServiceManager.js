import React from "react";
import { Platform } from "react-native";
import { showDialogue } from "../utils/EDAlert";
import { debugLog } from "./EDConstants";
// import I18n from "react-native-i18n";
import jstz from 'jstz';

import NetInfo from "@react-native-community/netinfo"


const timezoneCurrent = jstz.determine();

export const onInitialNetConnection = () => {
    // NetInfo.isConnected.removeEventListener(onInitialNetConnection);
    console.log("onInitialNetConnection CALLED");
};

export const netStatus = (callback) => {
    NetInfo.isConnected.fetch().then(
        (isConnected) => {
            callback(isConnected)
        }
    )
}


export const netStatusEvent = (callback) => {
    NetInfo.isConnected.addEventListener(
        'connectionChange',
        (status) => {
            callback(status)
        }
    )
}

/**
 * Generic function to make api calls with method post
 * @param {apiPost} url  API end point to call
 * @param {apiPost} responseSuccess  Call-back function to get success response from api call
 * @param {apiPost} responseErr  Call-back function to get error response from api call
 * @param {apiPost} requestHeader  Request header to be send to api
 * @param {apiPost} body data to be send through api
 */
export async function apiPost(
    url,
    body,
    responseSuccess,
    responseErr,
    requestHeader
) {
    if (requestHeader == undefined) {
        showDialogue("REQUEST HEADER NOT DEFINED IN " + url);
        responseSuccess({});
        return;
    }

    let formdata = new FormData();

    Object.keys(body || {}).map(keyToCheck => {
        formdata.append(keyToCheck, body[keyToCheck])
    })

    formdata.append("user_timezone", timezoneCurrent.name() || "")

    debugLog("========== REQUEST URL ==========", url)
    debugLog("========== REQUEST PARAMS ==========", JSON.stringify(formdata))

    fetch(url, {
        method: "POST",
        headers: requestHeader,
        body: formdata
    })
        .then(errorHandler)
        .then(response => response.json())
        .then(responseFetched => checkAPIStatus(responseFetched))
        .then(responseProcessed =>
            responseSuccess(responseProcessed.data, responseProcessed.message)
        )
        .catch(err => responseErr(err.data, err.message));
}

export async function apiPostForFileUpload(
    url,
    body,
    imageURI,
    responseSuccess,
    responseErr
) {
    let formdata = new FormData();

    if (imageURI) {
        // debugLog("imageURI ::: ", JSON.stringify(imageURI))
        // debugLog("length ::: ", imageURI.length)
    }
    Object.keys(body || {}).map(keyToCheck => {
        formdata.append(keyToCheck, body[keyToCheck])
    })
    formdata.append("user_timezone", timezoneCurrent.name() || "")


    if (imageURI != undefined && imageURI != null) {

        // debugLog("imageURI inside IF condition")
        if (imageURI != undefined && imageURI.uri != undefined) {
            // debugLog("imageURI.uri ::::: ", imageURI.uri)
        }

        const uriParts = imageURI.fileName ? imageURI.fileName.split(".") : imageURI.uri.split(".")
        strURIToUse = Platform.OS == "ios" ? imageURI.uri.replace("file://", "") : imageURI.uri

        formdata.append("profile_pic", {
            uri: strURIToUse,
            name: imageURI.fileName || Math.round(new Date().getTime() / 1000),
            type: `image/${uriParts[uriParts.length - 1]}`
        });
        // debugLog("strURIToUse ::: ", strURIToUse)

        // debugLog("name ::: ", imageURI.fileName || Math.round(new Date().getTime() / 1000))

        // const uriParts = imageURI.split(".");  
        // const fileType = uriParts.length > 0 ? uriParts[uriParts.length - 1] : "jpeg";
        // formdata.append("profile_pic", {
        //     uri: imageURI,
        //     name: `testPhotoName.${fileType}`,
        //     type: `image/${fileType}`
        // });
    }

    // debugLog("========== REQUEST URL ==========", url)
    // debugLog("========== REQUEST PARAMS ==========", JSON.stringify(formdata))

    fetch(url, {
        method: "POST",
        body: formdata,
        headers: {
            Sessiontoken: "vMoDbd5W5Z3JJVhRPhZbIpnWIlR1MX",
            Accept: "application/json",
            "Content-Type": "multipart/form-data"
        }
    })
        .then(errorHandler)
        .then(response => response.json())
        .then(responseFetched => checkAPIStatus(responseFetched))
        .then(responseProcessed =>
            responseSuccess(responseProcessed.data, responseProcessed.message)
        )
        .catch(err => responseErr(err));
}

//Error Handler
/**
 *
 * @param {errorHandler} response Generic function to handle error occur in api
 */
const errorHandler = response => {

    if (
        (response.status >= 200 && response.status < 300) ||
        response.status == 401 ||
        response.status == 400
    ) {
        return Promise.resolve(response);
    } else {
        var error = new Error(response.statusText || response.status);
        error.response = response;
        return Promise.reject(error);
    }
};

const checkAPIStatus = reponseFetched => {
    debugLog("========== reponseFetched ==========", reponseFetched)
    let status = reponseFetched.status || false;
    if (status) {
        return Promise.resolve({
            data: reponseFetched || {},
            message: reponseFetched.message || ""
        });
    } else {
        return Promise.reject({
            data: reponseFetched || {},
            message: reponseFetched.message || ""
        });
    }

};
