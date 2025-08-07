import eslint from "@eslint/js";
import eslintConfigPrettier from "eslint-config-prettier";
import eslintPluginCypress from "eslint-plugin-cypress/flat";
import globals from "globals";
import babelParser from "@babel/eslint-parser"; // Import Babel parser

export default [
  {
    ignores: ["**/node_modules/*", "**/vendor/*", "**/dist/*"],
  },
  eslint.configs.recommended,
  eslintConfigPrettier,
  eslintPluginCypress.configs.recommended,
  {
    languageOptions: {
      ecmaVersion: 2022,
      sourceType: "module",
      parser: babelParser, // Use the imported parser
      parserOptions: {
        requireConfigFile: false, // Skip the need for a Babel config
        babelOptions: {
          plugins: [["@babel/plugin-proposal-decorators", { legacy: true }]], // Enable decorators
        },
      },
      globals: {
        ...trimKeys(globals.browser),
        ...trimKeys(globals.node),
        jQuery: true,
        $home: "readonly",
        wp: "readonly",
        mapboxgl: "readonly",
      },
    },
    rules: {
      "no-useless-escape": "error",
      "no-undef": "error",
      "no-unused-vars": "off",
      "no-empty": ["warn", { allowEmptyCatch: true }],
      "no-prototype-builtins": ["off"],
      "no-async-promise-executor": ["off"],
      "getter-return": "off",
      "no-func-assign": "off",
      "no-cond-assign": "off",
      "cypress/no-assigning-return-values": "off",
      "cypress/unsafe-to-chain-command": "off",
      "cypress/no-unnecessary-waiting": "off",
    },
  },
];

function trimKeys(source) {
  return Object.keys(source).reduce((acc, key) => {
    acc[key.trim()] = source[key];
    return acc;
  }, {});
}
