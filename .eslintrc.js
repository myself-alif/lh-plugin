module.exports = {
    env: {
        browser: true,
        es2021: true,
        jquery: true,
    },
    globals: {
        Splide: 'readonly',
        Vimeo: 'readonly',
        mapboxgl: 'readonly',
        waktools: 'writable',
        wpNavMenu: 'writable',
        lh_menu_depths: 'writable',
        WebKitCSSMatrix: 'writable',
    },
    extends: 'standard',
    overrides: [
        {
            env: {
                node: true,
            },
            files: ['.eslintrc.{js,cjs}'],
            parserOptions: {
                sourceType: 'script',
            },
        },
    ],
    parserOptions: {
        ecmaVersion: 'latest',
    },
    rules: {
        indent: ['error', 4],
        semi: ['error', 'always'],
        'semi-style': ['error', 'last'],
        camelcase: 0,
        'no-unused-vars': [
            'error',
            {
                vars: 'all',
                args: 'after-used',
                ignoreRestSiblings: false,
                varsIgnorePattern: 'main|gravity_forms|admin_limit_menu_depth|admin_acf'
            }
        ],
        'comma-dangle': 0,
        'object-shorthand': 'off'
    },
};
