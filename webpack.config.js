const path = require("path");
const defaults = require("@wordpress/scripts/config/webpack.config");

module.exports = {
  ...defaults,
  entry: {
    index: path.resolve(process.cwd(), "assets/ts/index.ts"),
  },
  output: {
    filename: "[name].js",
    path: path.resolve(process.cwd(), "build"),
  },
  module: {
    ...defaults.module,
    rules: [
      ...defaults.module.rules,
      {
        test: /\.tsx?$/,
        use: [
          {
            loader: "ts-loader",
            options: {
              configFile: "tsconfig.json",
            },
          },
        ],
      },
    ],
  },
  resolve: {
    extensions: [
      ".ts",
      ".tsx",
      ...(defaults.resolve
        ? defaults.resolve.extensions || [".js", ".jsx"]
        : []),
    ],
  },
  plugins: [...defaults.plugins],
};
