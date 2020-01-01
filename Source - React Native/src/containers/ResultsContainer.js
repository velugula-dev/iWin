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
    BackHandler,
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
import Assets from "../assets";
import { debugLog, GET_QUIZ_HISTORY_URL, GET_QUIZ_RESULTS_URL, API_PAGE_SIZE } from "../utils/EDConstants";
import EDPlaceholderView from "../components/EDPlaceholderView";
import { Messages } from "../utils/Messages";
import { apiPost, netStatus } from "../utils/ServiceManager";
import { connect } from "react-redux";
import { NavigationEvents } from "react-navigation";
import { removeSelectedQuizFromRedux } from "../redux/actions/QuizAction";
import MyWebView from "react-native-webview-autoheight";
import EDHThemeButton from "../components/EDThemeButton";
import { showDialogue } from "../utils/EDAlert";


class ResultItem extends React.PureComponent {
    render() {
        item = this.props.itemToDisplay
        index = this.props.index
        isCurrentUser = item.user_id == this.props.loggedInUserId
        textColor = isCurrentUser ? EDColors.white : EDColors.text
        fontToSet = isCurrentUser ? EDFonts.bold : EDFonts.regular

        return (
            <View style={{
                flex: 1,
                backgroundColor: isCurrentUser
                    ? EDColors.metallicGold
                    : (index % 2 == 0 ? EDColors.white : EDColors.palePrimary),
                alignItems: 'center',
            }}>
                <TouchableOpacity style={{ flex: 1, flexDirection: isRTL ? 'row-reverse' : 'row', alignItems: 'center' }}>
                    <Text style={{ color: textColor, textAlign: 'center', flex: 1, fontFamily: fontToSet, fontSize: heightPercentageToDP("1.9%") }}>{index + 1}</Text>
                    <Text style={{ color: textColor, marginVertical: 20, marginLeft: 10, marginRight: 5, flex: 4, fontFamily: fontToSet, fontSize: heightPercentageToDP("1.9%") }}>{item.name}</Text>
                    <Text style={{ color: textColor, textAlign: 'center', flex: 2, fontFamily: fontToSet, fontSize: heightPercentageToDP("1.9%") }}>{item.points}</Text>
                    <Text style={{ color: textColor, textAlign: 'center', flex: 2, fontFamily: fontToSet, fontSize: heightPercentageToDP("1.9%") }}>{item.total}</Text>
                </TouchableOpacity>
            </View>
        );
    }
}


class ResultsContainer extends React.Component {
    constructor(props) {
        super(props);
        this.isScrolling = false;
        // this.quizDate = this.props.navigation.state.params.quizSelected ? this.props.navigation.state.params.quizSelected.question_date : ""
        this.quizDate = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date
            : this.props.screenProps.payload.data.notification_date;
        this.quizDateWithTime = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date_time
            : this.props.screenProps.payload.data.notification_date_time;

        this.customStyle =
            "<style>* {max-width: 100%;} body {font-size: 45px;font-family:Ubuntu-Regular}</style>";
        this.refreshIntervalID = undefined;

    }

    state = {
        isLoading: false,
        currentPage: 1,
        strOnScreenMessage: "",
        arrayResults: undefined,

    }

    onDidFocusResultsContainer = () => {
        BackHandler.addEventListener('hardwareBackPress', this.handleBackPress);
        this.callQuizResultsAPI()
    }

    onDidBlurResultsContainer = () => {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackPress);
    }

    handleBackPress = () => {
        BackHandler.removeEventListener('hardwareBackPress', this.handleBackPress);
        this.navigateToPreviousScreen()
        return true;
    }

    componentDidMount() {
    }

    callQuizResultsAPI() {
        debugLog("RESULTS SCREEN PROPS ::: ", this.props)

        // this.quizDate = (this.props.screenProps && this.props.screenProps.payload && this.props.screenProps.payload.data)
        //     ? this.props.screenProps.payload.data.notification_date || ""
        //     : this.props.navigation.state.params.question_date

        this.quizDate = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date
            : this.props.screenProps.payload.data.notification_date;
        this.quizDateWithTime = this.props.navigation.state.params
            ? this.props.navigation.state.params.question_date_time
            : this.props.screenProps.payload.data.notification_date_time;

        this.props.screenProps.payload = { data: { notification_date: this.quizDate, notification_date_time: this.quizDateWithTime } }




        netStatus(status => {
            if (status) {

                this.setState({ isLoading: true, strOnScreenMessage: '' })
                pageNumberToPass = ((this.state.arrayResults || []).length / API_PAGE_SIZE) + 1
                let resultsParams = {
                    exam_date: this.quizDateWithTime,
                    user_id: this.props.userDetails.user_id || "",
                    display_per_page: API_PAGE_SIZE,
                    page_no: pageNumberToPass
                };


                // showDialogue("RESULTS API PARAMS :::: " + JSON.stringify(resultsParams))
                apiPost(
                    GET_QUIZ_RESULTS_URL,
                    resultsParams,
                    (dictSuccess, message) => {
                        if (dictSuccess.quiz_result_data != undefined) {
                            this.isScrolling = dictSuccess.quiz_result_data.length >= API_PAGE_SIZE

                            if (
                                dictSuccess.quiz_result_data.length > 0 &&
                                this.state.arrayResults == undefined
                            ) {
                                this.state.arrayResults = [];
                            }

                            if (dictSuccess.quiz_result_data.length > 0) {
                                this.setState({
                                    arrayResults: [
                                        ...this.state.arrayResults,
                                        ...dictSuccess.quiz_result_data
                                    ], isLoading: false, strOnScreenMessage: message
                                });
                            } else {
                                this.setState({
                                    isLoading: false, strOnScreenMessage: message
                                });

                            }


                        } else {

                            if (
                                dictSuccess.quiz_result_data && dictSuccess.quiz_result_data.length > 0 &&
                                this.state.arrayResults == undefined
                            ) {
                                this.state.arrayResults = [];
                            }

                            this.setState({
                                arrayResults: [
                                    ...this.state.arrayResults,
                                    ...dictSuccess.quiz_result_data
                                ], isLoading: false, strOnScreenMessage: message
                            });
                        }
                    },
                    (dictFailure, message) => {
                        if (this.state.arrayResults == undefined) {
                            this.state.arrayResults = [];
                        }
                        this.setState({
                            arrayResults: [
                                ...this.state.arrayResults,
                                ...(dictFailure != undefined ? dictFailure.quiz_result_data || [] : [])
                            ], isLoading: false, strOnScreenMessage: message
                        });
                        // this.setState({ isLoading: false, strOnScreenMessage: message || Messages.generalWebServiceError })
                    },
                    {}
                );
            } else {
                this.setState({ isLoading: false, strOnScreenMessage: Messages.noInternet })
                // showNoInternetAlert()
            }
        })
    }

    renderDay = (resultToRender) => {
        return <ResultItem index={resultToRender.index} itemToDisplay={resultToRender.item} loggedInUserId={this.props.userDetails.user_id}></ResultItem>
    }

    navigateToPreviousScreen = () => {
        this.props.screenProps.payload.data.notification_date = undefined
        this.props.screenProps.payload.data.notification_date_time = undefined

        this.props.screenProps.payload.data.question_date = undefined
        this.props.screenProps.payload.data.question_date_time = undefined

        this.props.removeSelectedQuizDateFromReduxOnBackButtonResultsContainer()
        this.props.navigation.pop()
    }

    flatListEndReached = () => {


        pageNumberToPass = (this.state.arrayResults || []).length / API_PAGE_SIZE

        if (this.isScrolling && pageNumberToPass != 0) {
            if (this.refreshIntervalID) {
                clearInterval(this.refreshIntervalID)
            }
            this.refreshIntervalID = setTimeout(() => {
                this.callQuizResultsAPI()
            }, 1000);
        }
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
                title={strings("ScreenTitles.results") + (" ( " + this.quizDate + " )")}
                loading={this.state.isLoading}
                left={Assets.back}
                onLeft={this.navigateToPreviousScreen}
                right={this.props.infoText ? Assets.infoicon : null}
                onRight={this.buttonInfoPressed}
            >

                <NavigationEvents onDidFocus={this.onDidFocusResultsContainer} onDidBlur={this.onDidBlurResultsContainer} />

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

                                {/* <View style={{
                                backgroundColor: "#fff",
                                padding: 10,
                                marginLeft: 20,
                                marginRight: 20,
                                borderRadius: 6,
                                width: Dimensions.get("window").width - 40,
                                height: Dimensions.get("window").height - 80,
                                marginTop: 20,
                                marginBottom: 20,
                                justifyContent: 'center'
                            }}> */}



                                <Text style={{ marginHorizontal: 20, marginTop: 20, marginBottom: 10, textAlign: 'center', fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2.3%") }}>{this.props.infoText.cms_title}</Text>



                                {/* <ScrollView style={{ marginVertical: 10, marginHorizontal: 10, flex: 1 }}>
                                    <Hyperlink linkStyle={{ color: EDColors.primary }} linkDefault={true}>
                                        <Text style={{ margin: 20, fontFamily: EDFonts.regular, fontSize: heightPercentageToDP("2.1%") }}>{(this.isInfoViewGlobalMessage ? this.props.infoText.cms_contents : currentQuestion.question_detail) + "\n\n" + "Read more at http://www.google.com"}</Text>
                                    </Hyperlink>
                                </ScrollView> */}


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
                                        debugLog("::::: event :::::", JSON.stringify(event))
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
                {this.state.arrayResults != undefined
                    ?
                    (this.state.arrayResults.length > 0
                        ? <View style={{ flex: 1 }}>

                            <EDRTLView style={{ backgroundColor: EDColors.palePrimary, alignItems: 'center' }}>
                                <Text style={{ textAlign: 'center', flex: 1, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Rank"}</Text>
                                <Text style={{ marginVertical: 20, marginLeft: 10, marginRight: 5, flex: 4, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Name"}</Text>
                                <Text style={{ textAlign: 'center', flex: 2, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Points"}</Text>
                                <Text style={{ textAlign: 'center', flex: 2, fontFamily: EDFonts.medium, fontSize: heightPercentageToDP("2%") }}>{"Total"}</Text>
                            </EDRTLView>
                            <FlatList
                                style={{ flex: 1 }}
                                data={this.state.arrayResults}
                                extraData={this.state}
                                onEndReached={this.flatListEndReached}
                                onEndReachedThreshold={0.5}
                                keyExtractor={(item, index) => item + index}
                                showsVerticalScrollIndicator={false}
                                style={{ backgroundColor: '#fff' }}
                                renderItem={this.renderDay}
                            />

                        </View>
                        : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />
                    )
                    : <EDPlaceholderView messageToDisplay={this.state.strOnScreenMessage} />
                }



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
            quizSelectedInRedux: state.quizReducer,
            userDetails: state.userOperation,
            infoText: state.generalReducer

        }
    },
    dispatch => {
        return {
            removeSelectedQuizDateFromReduxOnBackButtonResultsContainer: () => {
                dispatch(removeSelectedQuizFromRedux())
            }
        }
    }
)(ResultsContainer);