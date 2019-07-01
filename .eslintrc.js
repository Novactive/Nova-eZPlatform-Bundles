module.exports = {
    "parser": '@typescript-eslint/parser',
    "env": {
        "browser": true,
        "es6": true
    },
    "extends": [
      "standard",
      "plugin:react/recommended",
      'plugin:@typescript-eslint/recommended'
    ],
    "globals": {
        "Atomics": "readonly",
        "SharedArrayBuffer": "readonly"
    },
    "parserOptions": {
        "ecmaFeatures": {
            "jsx": true
        },
        "ecmaVersion": 2018,
        "sourceType": "module"
    },
    "plugins": [
        "react",
        '@typescript-eslint'
    ],
    "rules": {
        // "indent": true
        // "semi": true
    }
}
