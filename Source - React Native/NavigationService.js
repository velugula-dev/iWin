// It is used when we have to navigate to a screen from a file which is not in our Root navigator
// We have used it in our App.js file.
import { NavigationActions, StackActions } from 'react-navigation';
import { showDialogue } from './src/utils/EDAlert';
import { NotificationTypes } from './src/utils/EDConstants';
import Moment from "moment";
import moment from 'moment';

let _navigator;

function setTopLevelNavigator(navigatorRef) {
    _navigator = navigatorRef;
}

function navigate(routeName, params) {
    _navigator.dispatch(
        NavigationActions.navigate({
            routeName,
            params,
        })
    );
}


function reloadStackForQuizScreen(navigationController, params) {


    var validData = params.data || {}
    var validNotificationType = validData.notification_type || ""

    if (validNotificationType.length == 0) {
        showDialogue("Notification type not defined for payload :: " + JSON.stringify(validData))
        return
    }




    var notificationPayloadDate = validData.notification_date
    var notificationPayloadDateWithTime = validData.notification_date_time || ""

    var datePayload = Moment.utc(notificationPayloadDate).toDate()
    var localDatePayload = moment(datePayload).local().format('YYYY-MM-DD');

    var dateTimePayload = Moment.utc(notificationPayloadDateWithTime).toDate()
    var localDateTimePayload = moment(dateTimePayload).local().format('YYYY-MM-DD HH:mm:ss');


    // var gmtDateTime = Moment.utc(notificationPayloadDate, "YYYY-MM-DD")
    // var gmtDateTimeNew = Moment.utc(notificationPayloadDateWithTime, "YYYY-MM-DD")
    // var localConvertedTime = gmtDateTime.local().format('YYYY-MM-DD');
    // var localConvertedDateWithTimeNew = gmtDateTimeNew.local().format('YYYY-MM-DD HH:mm:ss') || "N/A";

    var localConvertedDate = localDatePayload;
    var localConvertedTime = localDateTimePayload;
    if(localConvertedTime != undefined && localConvertedDate != undefined && !localConvertedTime.toLowerCase().includes("invalid") && !localConvertedTime.includes(localConvertedDate)) {
        if(localConvertedTime.split(" ").length > 0) {
            localConvertedDate = localConvertedTime.split(" ")[0]
        }
    }


    // showDialogue("JSON Payload 1234 ::: " + JSON.stringify(params.data) + "\n\n" + "LOCAL DATE  == " + localConvertedDate + "\n\n" + "LOCAL DATE TIME == " + localConvertedTime + "\n\n" + "abc123 :: " + dateTimePayload + "\n\n" + "xyz123 :: " + localDateTimePayload) // 2019-10-19


    switch (validNotificationType.toLowerCase()) {
        case NotificationTypes.questionsPosted:
            _navigator.dispatch(
                StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home", params: {} })]
                })
            );
            break;

        case NotificationTypes.answersPosted:
            // CHANGE THE ROOT SCREEN
            _navigator.dispatch(
                StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home" })]
                })
            );


            // navigationController.navigate("Home")
            _navigator.dispatch(
                NavigationActions.navigate({
                    routeName: "QuizDetails",
                    action: NavigationActions.navigate({
                        routeName: "Results",
                        params: {
                            question_date: localConvertedDate || params.data.notification_date,
                            question_date_time: localConvertedTime || params.data.notification_date_time,
                            totalquestions: 4
                        }
                    })
                })
            );
            break;

        case NotificationTypes.feedback:
            // CHANGE THE ROOT SCREEN
            _navigator.dispatch(
                StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home" })]
                })
            );


            // navigationController.navigate("Home")
            _navigator.dispatch(
                NavigationActions.navigate({
                    routeName: "QuizDetails",
                    action: NavigationActions.navigate({
                        routeName: "Feedback",
                        params: {
                            question_date: localConvertedDate || params.data.notification_date,
                            question_date_time: localConvertedTime || params.data.notification_date_time,
                            totalquestions: 4
                        }
                    })
                })
            );
            break;

        case NotificationTypes.winnersAnnouncement:
            // CHANGE THE ROOT SCREEN
            _navigator.dispatch(
                StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home" })]
                })
            );


            // navigationController.navigate("Home")
            _navigator.dispatch(
                NavigationActions.navigate({
                    routeName: "QuizDetails",
                    action: NavigationActions.navigate({
                        routeName: "Results",
                        params: {
                            question_date: localConvertedDate || params.data.notification_date,
                            question_date_time: localConvertedTime || params.data.notification_date_time,
                            totalquestions: 4
                        }
                    })
                })
            );
            break;

        case NotificationTypes.custom:
            _navigator.dispatch(
                StackActions.reset({
                    index: 0,
                    key: null,
                    actions: [NavigationActions.navigate({ routeName: "Home", params: {} })]
                })
            );
            break;

        default:
            break;
    }






    // _navigator.dispatch(
    //     NavigationActions.navigate({
    //         routeName,
    //         params,
    //     })
    // );
}


// add other navigation functions that you need and export them

export default {
    navigate,
    setTopLevelNavigator,
    reloadStackForQuizScreen
};  