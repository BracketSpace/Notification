const defaultConfig = require("@micropackage/scripts/config/webpack.config");

const entry = {};

for (const key in defaultConfig.entry) {
	entry[key.replace("src", "dist")] = defaultConfig.entry[key];
}

module.exports = {
	...defaultConfig,
	entry
};
