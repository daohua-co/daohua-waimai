module.exports = {
  env: {
    browser: true,
    es2021: true
  },
  extends: [
    'plugin:vue/vue3-essential',
    // 'standard-with-typescript' // npm: eslint-config-standard-with-typescript
  ],
  overrides: [],
  parserOptions: {
    ecmaVersion: 'latest',
    sourceType: 'module'
  },
  plugins: [
    'vue'
  ],
  rules: {
    indent: ['warn', 2, {"SwitchCase": 1}], // 2空格缩进阅读代码速度更快
    semi: ['warn', 'always'],
    'vue/multi-word-component-names': 'off',
    'space-before-function-paren': ['warn', 'never']
  }
};
