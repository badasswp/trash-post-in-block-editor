module.exports = {
	verbose: true,
	preset: '@wordpress/jest-preset-default',
	testEnvironment: 'jsdom',
	setupFilesAfterEnv: [ './jest.setup.js' ],
	globals: {
		window: {},
	},
	transform: {
		'^.+\\.[jt]sx?$': 'babel-jest',
	},
	moduleNameMapper: {
		uuid: require.resolve( 'uuid' ),
		'\\.(css|less|scss|sass)$': 'identity-obj-proxy',
	},
	modulePathIgnorePatterns: [ '/__snapshots__/' ],
};
