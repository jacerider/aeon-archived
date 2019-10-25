module.exports = {
  extends: "stylelint-config-standard",
  plugins: [
    "stylelint-order",
    "stylelint-scss",
  ],
  "rules": {
    "at-rule-blacklist": [
      "debug",
    ],
    "at-rule-no-unknown": [true, {
      "ignoreAtRules": [
        "extends",
        "ignores",
        "mixin",
        "function",
        "include",
        "if",
        "each",
        "else",
        "warn",
        "return",
        "error",
        "extend",
      ]
    }],
    "at-rule-empty-line-before": [
      "always",
      {
        "except": [
          "first-nested",
        ],
        "ignore": [
          "after-comment",
          "blockless-after-blockless"
        ],
        "ignoreAtRules": [
          "extends",
          "ignores",
          "mixin",
          "function",
          "include",
          "if",
          "each",
          "else",
          "warn",
          "return",
          "error",
          "extend",
        ]
      }
    ],
    "block-no-empty": true,
    "color-no-hex": true,
    "color-named": ["never", {
      "message": "Avoid using color literals."
    }],
    "declaration-block-trailing-semicolon": "always",
    "declaration-empty-line-before": [
      "never",
      // {
      //   "except": [
      //     "after-declaration",
      //     "first-nested"
      //   ],
      // }
    ],
    "declaration-no-important": [true, {
      "message": "There's an !important tag. Is that supposed to be there?"
    }],
    "declaration-property-value-blacklist": [{
      '/^border/': ['none']
    }, { severity: 'warning' }],
    "function-parentheses-space-inside": "never-single-line",
    "function-url-quotes": ["always",
      {
        "message": "URLs should be wrapped in quotes",
        "severity": 'warning',
      }
    ],
    "indentation": [2,
      {
        "severity": 'warning',
      }
    ],
    "length-zero-no-unit": [true, {
      "message": "A length set to 0 does not need a unit."
    }],
    "max-nesting-depth": 8,
    "no-descending-specificity": null,
    "no-missing-end-of-source-newline": true,
    "number-leading-zero": "always",
    "property-no-vendor-prefix": [true,
      {
        "severity": 'warning',
      }
    ],
    "rule-empty-line-before": [
      "always",
      {
        "ignore": [
          "after-comment"
        ]
      }
    ],
    "selector-class-pattern": /^([a-zA-Z0-9_-]+-?)+$/,
    "selector-id-pattern": /^([a-zA-Z0-9_]+-?)+$/,
    "selector-max-id": [0, {
      "message": "There's an ID being used. Is that supposed to be there?"
    }],
    "selector-no-qualifying-type": [true,
      {
        "ignore": [
          "attribute",
          "class",
        ]
      }
    ],
    "shorthand-property-no-redundant-values": [true,
      {
        "severity": 'warning',
      }
    ],
    "selector-pseudo-element-colon-notation": "single",
    "string-quotes": ["single", {
      "message": "Single quotes should be used."
    }],
    "unit-whitelist": [
      "px",
      "%",
      "em",
      "rem",
      "s",
      "ms",
      "deg",
      "vw",
      "vh",
    ],
    "order/order": [
      {
        type: 'at-rule',
        name: 'include',
      },
      'declarations',
      {
        type: 'at-rule',
        name: 'include',
        parameter: 'breakpoint',
      },
    ],
    "scss/at-extend-no-missing-placeholder": true,
    "scss/dollar-variable-pattern": /^[a-z\-]+$/,
    "scss/at-function-parentheses-space-before": "never",
    "scss/at-function-pattern": /^[a-z\-]+$/,
    "scss/at-mixin-pattern": /^[a-z\-]+$/,
    "scss/at-mixin-argumentless-call-parentheses": "never",
    // "scss/operator-no-newline-before": true,
    // "scss/operator-no-newline-after": true,
    // "scss/operator-no-unspaced": true,
    "scss/percent-placeholder-pattern": /^([a-z0-9]+-?)+$/,

    // "at-rule-no-unknown": true,
    // "color-no-invalid-hex": true,
    // "comment-no-empty": true,
    // "declaration-block-no-duplicate-properties": [
    //   true,
    //   {
    //     "ignore": [
    //       "consecutive-duplicates-with-different-values"
    //     ]
    //   }
    // ],
    // "declaration-block-no-shorthand-property-overrides": true,
    // "font-family-no-duplicate-names": true,
    // "font-family-no-missing-generic-family-keyword": true,
    // "function-calc-no-unspaced-operator": true,
    // "function-linear-gradient-no-nonstandard-direction": true,
    // "keyframe-declaration-no-important": true,
    // "media-feature-name-no-unknown": true,
    // "no-duplicate-at-import-rules": true,
    // "no-duplicate-selectors": true,
    // "no-empty-source": true,
    // "no-extra-semicolons": true,
    // "no-invalid-double-slash-comments": true,
    // "property-no-unknown": true,
    // "selector-pseudo-class-no-unknown": true,
    // "selector-pseudo-element-no-unknown": true,
    // "selector-type-no-unknown": true,
    // "string-no-newline": true,
    // "unit-no-unknown": true,
    // "at-rule-name-case": "lower",
    // "at-rule-name-space-after": "always-single-line",
    // "at-rule-semicolon-newline-after": "always",
    // "block-closing-brace-empty-line-before": "never",
    // "block-closing-brace-newline-after": "always",
    // "block-closing-brace-newline-before": "always-multi-line",
    // "block-closing-brace-space-before": "always-single-line",
    // "block-opening-brace-newline-after": "always-multi-line",
    // "block-opening-brace-space-after": "always-single-line",
    // "block-opening-brace-space-before": "always",
    // "color-hex-case": "lower",
    // "color-hex-length": "short",
    // "comment-empty-line-before": [
    //   "always",
    //   {
    //     "except": [
    //       "first-nested"
    //     ],
    //     "ignore": [
    //       "stylelint-commands"
    //     ]
    //   }
    // ],
    // "comment-whitespace-inside": "always",
    // "custom-property-empty-line-before": [
    //   "always",
    //   {
    //     "except": [
    //       "after-custom-property",
    //       "first-nested"
    //     ],
    //     "ignore": [
    //       "after-comment",
    //       "inside-single-line-block"
    //     ]
    //   }
    // ],
    // "declaration-bang-space-after": "never",
    // "declaration-bang-space-before": "always",
    // "declaration-block-semicolon-newline-after": "always-multi-line",
    // "declaration-block-semicolon-space-before": "never",
    // "declaration-block-single-line-max-declarations": 1,
    // "declaration-colon-newline-after": "always-multi-line",
    // "declaration-colon-space-after": "always-single-line",
    // "declaration-colon-space-before": "never",
    // "function-comma-newline-after": "always-multi-line",
    // "function-comma-space-after": "always-single-line",
    // "function-comma-space-before": "never",
    // "function-max-empty-lines": 0,
    // "function-name-case": "lower",
    // "function-parentheses-newline-inside": "always-multi-line",
    // "function-whitespace-after": "always",
    // "max-empty-lines": 1,
    // "media-feature-colon-space-after": "always",
    // "media-feature-colon-space-before": "never",
    // "media-feature-name-case": "lower",
    // "media-feature-parentheses-space-inside": "never",
    // "media-feature-range-operator-space-after": "always",
    // "media-feature-range-operator-space-before": "always",
    // "media-query-list-comma-newline-after": "always-multi-line",
    // "media-query-list-comma-space-after": "always-single-line",
    // "media-query-list-comma-space-before": "never",
    // "no-eol-whitespace": true,
    // "number-leading-zero": "always",
    // "number-no-trailing-zeros": true,
    // "property-case": "lower",
    // "selector-attribute-brackets-space-inside": "never",
    // "selector-attribute-operator-space-after": "never",
    // "selector-attribute-operator-space-before": "never",
    // "selector-combinator-space-after": "always",
    // "selector-combinator-space-before": "always",
    // "selector-descendant-combinator-no-non-space": true,
    // "selector-list-comma-newline-after": "always",
    // "selector-list-comma-space-before": "never",
    // "selector-max-empty-lines": 0,
    // "selector-pseudo-class-case": "lower",
    // "selector-pseudo-class-parentheses-space-inside": "never",
    // "selector-pseudo-element-case": "lower",
    // "selector-type-case": "lower",
    // "unit-case": "lower",
    // "value-list-comma-newline-after": "always-multi-line",
    // "value-list-comma-space-after": "always-single-line",
    // "value-list-comma-space-before": "never",
    // "value-list-max-empty-lines": 0
  }
}