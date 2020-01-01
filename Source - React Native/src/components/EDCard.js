import React from 'react';
import { Platform } from "react-native";
import { Card } from "native-base";

export default class EDCard extends React.PureComponent {
    render() {
        return (
            <Card style={[this.props.style, { margin: Platform.OS === 'ios' ? 0 : 15 }]}>
                {this.props.children}
            </Card>
        );
    }
}