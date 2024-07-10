// Copyright (c) Microsoft Corporation.
// Licensed under the MIT License.

const defaults = require("@wordpress/scripts/config/webpack.config")

module.exports = {
    ...defaults,
    externals: {
        "react": "React",
        "react-dom": "ReactDOM",
    },
    entry: {
        ...defaults.entry,
        admin: ["./src/modules/admin/index.js"],
        apisList: ["./src/modules/apisList/index.js"],
        apiDetail: ["./src/modules/apiDetail/index.js"],
        productsList: ["./src/modules/productsList/index.js"],
        productDetail: ["./src/modules/productDetail/index.js"],
        profile: ["./src/modules/profile/index.js"],
        signInButton: ["./src/modules/signInButton/index.js"],
    },
    //    output: {
    //        ...defaults.output,
    //        path: __dirname + "/dist",
    //    },
}
