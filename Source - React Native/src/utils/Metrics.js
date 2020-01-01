"use strict";

import { Dimensions, Platform, StatusBar } from "react-native";

const { width, height } = Dimensions.get("window");

const Metrics = {
  screenWidth: width,
  screenHeight: height,
  statusbarHeight: Platform.OS === "ios" ? 20 : 20,
  // statusbarHeight: StatusBar.currentHeight,
  navbarHeight: 70,
  spinnerSize: width * 0.5
};

export default Metrics;
