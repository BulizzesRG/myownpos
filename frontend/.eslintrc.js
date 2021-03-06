module.exports = {
  root: true,
  env: {
    browser: true,
    node: true
  },
  parserOptions: {
    parser: '@babel/eslint-parser',
    requireConfigFile: false
  },
  extends: [
    '@nuxtjs',
    'plugin:nuxt/recommended'
  ],
  plugins: [
  ],
  // add your custom rules here
  rules: {
    "vue/html-indent": ["error", "tab", {
		"baseIndent": 0
	}],
    "vue/script-indent": ["error", "tab", {
    	"baseIndent": 0
    }],    
    "no-tabs": ["error", { "allowIndentationTabs": true }]
  }
}
