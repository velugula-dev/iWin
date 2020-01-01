import React from "react";
import {
    View,
    Text,
    StyleSheet,
    Image,
    KeyboardAvoidingView,
    Platform,
    FlatList,
    TouchableOpacity,
    Modal,
    Linking
} from "react-native";

import Metrics from "../utils/Metrics";
import { widthPercentageToDP as wp, heightPercentageToDP as hp, heightPercentageToDP } from 'react-native-responsive-screen';
import { EDColors } from "../utils/EDColors";
import { EDFonts } from "../utils/EDFontConstants";
import BaseContainer from "./BaseContainer";
import { strings, isRTL } from "../locales/i18n";
import EDRTLView from "../components/EDRTLView";
import { showNoInternetAlert } from "../utils/EDAlert";
import { GET_QUESTIONS_URL, GET_QUIZ_HISTORY_URL, API_PAGE_SIZE, debugLog } from "../utils/EDConstants";
import { connect } from "react-redux";
import { apiPost, netStatus } from "../utils/ServiceManager";
import EDPlaceholderView from "../components/EDPlaceholderView";
import { Messages } from "../utils/Messages";
import { NavigationEvents } from "react-navigation";
import { saveSelectedQuizInRedux } from "../redux/actions/QuizAction";
import MyWebView from "react-native-webview-autoheight";

import EDHThemeButton from "../components/EDThemeButton";
import Assets from "../assets";
import Moment from "moment";
import moment from "moment";

class QuizItem extends React.PureComponent {


    passSelectedQuizToContainer = () => {
        if (this.props.onQuizSelection != undefined) {
            this.props.onQuizSelection(this.props.itemToDisplay)
        }
    }

    render() {
        item = this.props.itemToDisplay
        index = this.props.index
        return (
            <TouchableOpacity onPress={this.passSelectedQuizToContainer} style={{ flex: 1, backgroundColor: index % 2 == 0 ? EDColors.white : EDColors.palePrimary, alignItems: 'center' }}>
                <View style={{ flex: 1, flexDirection: isRTL ? 'row-reverse' : 'row', alignItems: 'center' }}>
                    <Text style={{ textAlign: 'center', flex: 1, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("1.9%") }}>{index + 1}</Text>
                    <Text style={{ marginVertical: 20, marginLeft: 10, marginRight: 5, flex: 4, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("1.9%") }}>{item.question_date}</Text>
                    <Text style={{ textAlign: 'center', flex: 2, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("1.9%") }}>{item.totalquestions}</Text>
                </View>
            </TouchableOpacity>
        );
    }
}


class HistoryContainer extends React.Component {
    constructor(props) {
        super(props);
        this.fontSizeWebView = 45;//heightPercentageToDP("2.3%");
        this.customStyle =
            "<style>* {max-width: 100%;} body {color: #6B6B6B;font-size:" + this.fontSizeWebView + "px;font-family:Ubuntu-Regular}</style>";
    }

    state = {
        isLoading: false,
        currentPage: 1,
        strOnScreenMessage: "",
        arrayQuizes: undefined,
        shouldShowInfoView: false
    }

    componentDidMount() {
        // WEB VIEW FONT SIZE
    }

    displayResults = (quizSelected) => {
        // SAVE THE SELECTED QUIZ IN REDUX
        // this.props.saveQuizDetailsInRedux(quizSelected)
        // setTimeout(() => {
        debugLog("quizSelected BEFORE ::: " + JSON.stringify(quizSelected))
        // var hoursCheck = new Date().getHours(); //Current Hours
        // var minutesCheck = new Date().getMinutes(); //Current Minutes
        // var secCheck = new Date().getSeconds(); //Current Seconds
        var question_date = quizSelected.question_date
        var localDateTimeFromMoment = moment(new Date()).local().format("HH:mm:ss")
        quizSelected.question_date_time = question_date + " " + localDateTimeFromMoment
        // debugLog("hours ::: " + hoursCheck + "min ::: " + minutesCheck + "sec ::: " + secCheck + "localDateTimePayload ::: " + localDateTimePayload)
        debugLog("quizSelected AFTER ::: " + JSON.stringify(quizSelected))

        this.props.navigation.navigate("QuizDetails", quizSelected)
        // }, 2000);
    }

    renderDay(dayToRender, index) {
        return <QuizItem onQuizSelection={this.displayResults} index={index} itemToDisplay={dayToRender}></QuizItem>
    }

    onDidFocusHistoryContainer = () => {
        this.callQuizHistoryAPI()
    }


    callQuizHistoryAPI() {

        netStatus(status => {
            if (status) {

                this.setState({ isLoading: true, strOnScreenMessage: "" })
                let historyParams = {
                    user_id: this.props.userDetails.user_id,
                };


                apiPost(
                    GET_QUIZ_HISTORY_URL,
                    historyParams,
                    (dictSuccess, message) => {
                        if (dictSuccess.user_quiz_history_data != undefined) {
                            this.setState({ arrayQuizes: dictSuccess.user_quiz_history_data, isLoading: false, strOnScreenMessage: message })
                        }
                    },
                    (dictFailure, message) => {
                        this.setState({ isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                    },
                    {}
                );
            } else {
                this.setState({ strOnScreenMessage: Messages.noInternet })
                // showNoInternetAlert()
            }
        })
    }



    buttonInfoPressed = () => {
        this.setState({ shouldShowInfoView: !this.state.shouldShowInfoView })
    }

    dismissInfoView = () => {
        this.setState({ shouldShowInfoView: false })
    }

    render() {
        return (


            <BaseContainer
                title={strings("ScreenTitles.history")}
                right={this.props.infoText ? Assets.infoicon : null}
                onRight={this.buttonInfoPressed}
                loading={this.state.isLoading}
            >
                <NavigationEvents onDidFocus={this.onDidFocusHistoryContainer} />

                {(this.state.shouldShowInfoView)
                    ? <Modal visible={this.state.shouldShowInfoView}
                        animationType="slide"
                        transparent={true}
                        onRequestClose={this.dismissInfoView}>


                        <View style={{ flex: 1, backgroundColor: 'rgba(0,0,0,0.25)', justifyContent: "center" }}>

                            <View style={{
                                width: Metrics.screenWidth - 40,
                                height: Metrics.screenHeight - 2 * (heightPercentageToDP("10%") + 10),
                                marginHorizontal: 20, marginVertical: heightPercentageToDP("10%") + 10,
                                backgroundColor: "white",
                                justifyContent: 'center',
                                borderRadius: 8,
                            }}>


                                <Text style={{ marginHorizontal: 20, marginTop: 20, marginBottom: 10, textAlign: 'center', fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2.3%") }}>{this.props.infoText.cms_title}</Text>



                                <MyWebView
                                    source={{ html: this.customStyle + this.props.infoText.cms_contents }}
                                    ref={(ref) => { this.webview = ref; }}
                                    startInLoadingState={true}
                                    style={{
                                        flex: 1,
                                        alignSelf: "center",
                                        paddingBottom: Platform.OS == "ios" ? 0 : 15,
                                        backgroundColor: 'transparent'
                                    }}
                                    width="90%"
                                    dataDetectorTypes={["link"]}
                                    onLoadStart={() => {
                                        debugLog("::::: onLoadStart :::::")
                                    }}
                                    onLoadEnd={() => {
                                        debugLog("::::: onLoadEnd :::::")
                                    }}
                                    onError={(errorInWebView) => {
                                        debugLog("::::: errorInWebView :::::", JSON.stringify(errorInWebView))
                                    }}
                                    //hasIframe={true}
                                    scrollEnabled={true}
                                    onNavigationStateChange={(event) => {
                                        if (event.url != undefined && event.navigationType === 'click') {
                                            this.webview.stopLoading();
                                            Linking.openURL(event.url);
                                        }
                                    }}

                                />

                                <EDHThemeButton style={{ margin: 20 }}
                                    label={strings("General.dismiss")}
                                    onPress={this.dismissInfoView}
                                />


                            </View>
                        </View>
                        {/* </TouchableOpacity> */}

                    </Modal>
                    : null}

                {this.state.arrayQuizes != undefined
                    ?
                    (this.state.arrayQuizes.length > 0
                        ? <View style={{ flex: 1 }}>

                            <EDRTLView style={{ backgroundColor: EDColors.palePrimary, alignItems: 'center' }}>
                                <Text style={{ textAlign: 'center', flex: 1, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"#"}</Text>
                                {/* <Text style={{ textAlign: 'center', flex: 1, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Sr. No"}</Text> */}
                                <Text style={{ marginVertical: 20, marginLeft: 10, marginRight: 5, flex: 4, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Quiz Title"}</Text>
                                <Text style={{ textAlign: 'center', flex: 2, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Questions"}</Text>
                            </EDRTLView>
                            <FlatList
                                style={{ flex: 1 }}
                                data={this.state.arrayQuizes}
                                keyExtractor={(item, index) => item + index}
                                showsVerticalScrollIndicator={false}
                                style={{ backgroundColor: '#fff' }}
                                renderItem={({ item, index }) =>
                                    this.renderDay(item, index)
                                }
                            />

                        </View>
                        : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />
                    )
                    : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />}



            </BaseContainer>
        );
    }
}

const styles = StyleSheet.create({
    container: {
        flex: 1,
        alignItems: "center",
        backgroundColor: "#F5FCFF"
    }
});

export default connect(
    state => {
        return {
            userDetails: state.userOperation,
            infoText: state.generalReducer

        }
    },
    dispatch => {
        return {
            saveQuizDetailsInRedux: (selectedQuizData) => {
                dispatch(saveSelectedQuizInRedux(selectedQuizData))
            }
        }
    }
)(HistoryContainer);