const colors = require('tailwindcss/colors')

module.exports = {
  content: ["./templates/**/*.html.twig", "./assets/vue/**/*.vue"],
  theme: {
    colors: {
      transparent: 'transparent',
      current: 'currentColor',
      black: colors.black,
      white: colors.white,
      gray: colors.gray,
      emerald: colors.emerald,
      indigo: colors.indigo,
      yellow: colors.yellow,
      blue: colors.blue,
      red: colors.red,
      grey: colors.grey,
      cyan: colors.cyan,
      orange: colors.orange,
      green: colors.green,
      'prunoise': '#B40F69'
    },
    extend: {},
  },
  plugins: [],
}
