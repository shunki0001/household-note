import vue from "eslint-plugin-vue";
import prettier from "eslint-plugin-prettier";
import prettierConfig from "@vue/eslint-config-prettier";

const frontendFiles = ["resources/js/**/*.{js,ts,vue}"];
const vueFlatConfigs = vue.configs["flat/recommended"].map((config) => ({
    ...config,
    files: frontendFiles,
}));
const prettierFlatConfigs = (Array.isArray(prettierConfig) ? prettierConfig : [prettierConfig]).map(
    (config) => ({
        ...config,
        files: frontendFiles,
    }),
);

export default [
    {
        ignores: [
            "node_modules",
            "vendor",
            "storage",
            "bootstrap/cache",
            "public",
            "database",
            "tests",
            "dist",
        ],
    },
    ...vueFlatConfigs,
    {
        files: frontendFiles,
        rules: {
            "vue/multi-word-component-names": "off",
            "no-undef": "off",
        },
    },
    ...prettierFlatConfigs,
    {
        files: frontendFiles,
        plugins: { prettier },
        rules: {
            "prettier/prettier": "error",
        },
    },
];
