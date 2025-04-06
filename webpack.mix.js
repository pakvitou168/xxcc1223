const mix = require('laravel-mix');

const { styles } = require( '@ckeditor/ckeditor5-dev-utils' );

mix.override(webpackConfig => {
  const rules = webpackConfig.module.rules
  const targetSVG = /(\.(png|jpe?g|gif|webp|avif)$|^((?!font).)*\.svg$)/
  const targetFont = /(\.(woff2?|ttf|eot|otf)$|font.*\.svg$)/
  const targetCSS = /\.css$/

  // Exclude CK Editor regex from mix's default rules
  for (let rule of rules) {
    if (rule.test.toString() === targetSVG.toString()) {
      rule.exclude = /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/
    } else if (rule.test.toString() === targetFont.toString()) {
      rule.exclude = /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/
    } else if (rule.test.toString() === targetCSS.toString()) {
      rule.exclude = /ckeditor5-[^/\\]+[/\\].+\.css$/
    }
  }
})

/**
 * Webpack Config for CK Editor
 */
mix.webpackConfig({
  module: {
    rules: [
      {
        test: /ckeditor5-[^/\\]+[/\\]theme[/\\]icons[/\\][^/\\]+\.svg$/,
        use: ['raw-loader']
      },
      {
        test: /ckeditor5-[^/\\]+[/\\].+\.css$/,
        use: [
          {
            loader: 'postcss-loader',
            options: {
              postcssOptions: styles.getPostCssConfig({
                themeImporter: {
                  themePath: require.resolve('@ckeditor/ckeditor5-theme-lark')
                },
                minify: true
              })
            }
          }
        ]
      }
    ]
  }
})

mix.js('resources/js/app.js', 'public/js')
  .vue({
    options: {
      compilerOptions: {
        isCustomElement: (tag) => ['md-linedivider'].includes(tag),
      },
    },
  })
  .js('resources/js/script.js', 'public/js')

  .postCss("resources/css/print.css", "public/css/print.css", [
    require("tailwindcss"),
  ])
  .sass('resources/sass/app.scss', 'public/css')
  .postCss("resources/css/core.css", "public/css/core.css")
  .postCss("resources/css/app.css", "public/css", [
    require("tailwindcss"),
  ])
  .postCss("resources/css/form.css", "public/css/app.css")
  .copyDirectory('resources/fonts', 'public/fonts')
  .copyDirectory('resources/images', 'public/images')
  .webpackConfig(require('./webpack.config'));
