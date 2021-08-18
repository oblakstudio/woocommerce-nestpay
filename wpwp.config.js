/*
This is the main WP-Webpack config file.
Each config variable has a short explanation about what it controls
*/
module.exports = {
  /*
  Webpack entry points

  This is a list of 'main' source files we're compiling
  Feel free to rename / remove files you do not need.
  */
  entry: {
    main: [
      './scripts/main.ts',   // Frontend javascript
      './styles/main.scss',  // Frontend css
    ],
    admin: [
      './scripts/admin.ts',  // Admin Javascript
      './styles/admin.scss', // Admin CSS
    ],
  },
  /*
  File name format to use for production build

  All webpack merge tags are supported, but we use:
  [name] - filename
  [contenthash] - webpack hash based on the file content

  Default is [name]_[contenthash:5] - filename + 5 character hash. I.e. main_d56fg.css
  */
  cacheBusting: '[name].min',

  /*
  External global libraries
  */
  externals: {
    jquery: 'jQuery',
  },

  /*
  Root path for the asset files.

  If theme it should be: /wp-content/themes/theme-name
  If plugin it should be: /wp-content/plugins/plugin-name
  */
  publicPath: '/wp-content/plugins/woocommerce-nestpay-payment-gateway',

  /*
  Files to watch
  */
  watch: [
    'dist/**/**',
    'templates/**/*.php',
    'template-parts/**/*.php',
    'woocommerce/**/*.php',
    '*.php',
  ],

  /*
  Run linters during build process

  Will speed up building, but allows for style and code errors
  */
  lintOnBuild: true,

  devUrl: 'https://stup.test',

  translation: {
    domain: 'woocommerce-nestpay',
    filename: 'woocommerce-nestpay.pot',
    languageDir: './languages',
    bugReport: '',
    translator: 'Sibin Grasic <sibin.grasic@oblak.studio>',
    team: 'Oblak Studio <info@oblak.studio>',
  },
}
