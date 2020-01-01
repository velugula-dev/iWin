"use strict";

import React, { Component } from "react";
import {
  View,
  StyleSheet,
  TouchableOpacity,
  Image,
  Platform,
  Text,
  StatusBar
} from "react-native";
import Metrics from "../utils/Metrics";
import { Header, Title, Left, Right } from "native-base";
import Assets from "../assets";
import {
  widthPercentageToDP as wp,
  heightPercentageToDP as hp,
  heightPercentageToDP
} from "react-native-responsive-screen";
import { strings, isRTL } from "../locales/i18n";
import EDRTLImage from "./EDRTLImage";
import metrics from "../utils/Metrics";
import { EDColors } from "../utils/EDColors";
import { EDFonts } from "../utils/EDFontConstants";
import EDRTLView from "./EDRTLView";

export default class NavBar extends Component {
  render() {
    return (
      <View
        style={{
          height: hp("10%"),
          backgroundColor: EDColors.primary
        }}
      >
        <StatusBar barStyle="light-content" />

        {/* LEFT BUTTONS */}
        <EDRTLView style={{ paddingTop: Platform.OS == "ios" ? 20 : 0, flex: 1 }}>
          <EDRTLView style={{ flex: 4 }}>
            {this.props.left ? (
              <EDRTLView
                style={{ flex: 1, marginLeft: 10, alignItems: "center" }}
              >
                {this.props.isLeftString ? <Text style={{ flex: 1, fontFamily: EDFonts.medium, color: EDColors.white, fontSize: heightPercentageToDP("1.7%") }}>{this.props.left}</Text> : <TouchableOpacity style={{}} onPress={this.props.onLeft}>
                  <EDRTLImage
                    source={this.props.left}
                    style={styles.leftImage}
                    resizeMode="contain"
                  />
                </TouchableOpacity>}
              </EDRTLView>
            ) : null}
          </EDRTLView>

          {/* TITLE */}

          <View
            style={{ flex: 8, justifyContent: "center", alignItems: "center" }}
          >
            <Text
              numberOfLines={2}
              style={{
                textAlign: 'center',
                fontFamily: EDFonts.medium,
                fontSize: heightPercentageToDP("2.35%"),
                color: EDColors.white
              }}
            >
              {this.props.title}
            </Text>
          </View>

          {/* RIGHT BUTTONS */}
          <EDRTLView style={{ flex: 4 }}>
            {this.props.right ? (
              <EDRTLView
                style={{
                  justifyContent: "flex-end",
                  flex: 1,
                  marginRight: 10,
                  alignItems: "center"
                }}
              >
                <TouchableOpacity
                  style={{}}
                  onPress={this.props.onRight}
                >
                  <EDRTLImage
                    source={this.props.right}
                    style={styles.leftImage}
                    resizeMode="contain"
                  />
                </TouchableOpacity>
              </EDRTLView>
            ) : null}
          </EDRTLView>
        </EDRTLView>
      </View>
    );
  }
}

const styles = StyleSheet.create({
  topbar: {
    width: "100%",
    flex: 0,
    height: Metrics.navbarHeight + Metrics.statusbarHeight
  },
  navbar: {
    backgroundColor: EDColors.secondary,
    flex: 0,
    width: "100%",
    height: Metrics.navbarHeight,
    borderBottomColor: EDColors.primary,
    marginTop: Metrics.statusbarHeight + 10,
    borderBottomWidth: 0.5,
    flexDirection: "row",
    alignItems: "center",
    justifyContent: "space-between",
    paddingLeft: 5,
    paddingRight: 5
  },
  content: {
    position: "absolute",
    top: 0,
    left: 0,
    right: 0,
    bottom: 0,
    alignItems: "center",
    justifyContent: "center"
  },
  left: {
    color: EDColors.primary,
    height: 23,
    width: 23,
    resizeMode: "stretch"
  },
  leftImage: {}
});
