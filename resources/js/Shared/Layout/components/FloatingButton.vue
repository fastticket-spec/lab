<template>
    <nav class="iq-float-menu">
        <input type="checkbox" href="#" class="iq-float-menu-open" name="menu-open" id="menu-open" />
        <label class="iq-float-menu-open-button" for="menu-open">
            <span class="lines line-1"></span>
            <span class="lines line-2"></span>
            <span class="lines line-3"></span>
        </label>
        <button class="iq-float-menu-item bg-info"   @click="rtlChange(!rtl)" v-b-tooltip.hover title="Direction Mode" data-mode="rtl"><i :class="rtl ? 'ri-text-direction-l' : 'ri-text-direction-r'"></i></button>
        <button class="iq-float-menu-item bg-danger" @click="darkChange(!dark)"   v-b-tooltip.hover title="Color Mode" id="dark-mode" data-active="true"><i :class="dark ? 'ri-moon-line' : 'ri-sun-line'"></i></button>
        <button class="iq-float-menu-item bg-warning"  v-b-tooltip.hover title="Coming Soon"><i class="ri-palette-line"></i></button>
    </nav>
</template>
<script>
import darkLoader from '../../../../images/darkMode/dark-logo.gif'
import loader from '../../../../images/loader.gif'
export default {
  name: 'FloatingButton',
  data () {
    return {
      rtl: false,
      dark: false
    }
  },
  // computed: {
  //   ...mapGetters({
  //     darkMode: 'Setting/darkModeState',
  //     rtlMode: 'Setting/rtlModeState',
  //     colors: 'Setting/colorState'
  //   })
  // },
  methods: {
    changeColor (code) {
      document.documentElement.style.setProperty('--iq-primary', code.primary)
      document.documentElement.style.setProperty('--iq-primary-light', code.primaryLight)
      document.documentElement.style.setProperty('--iq-primary-dark', code.primaryDark)
    },
    reset () {
      this.changeColor({
        primary: '#827af3',
        primaryLight: '#b47af3',
        bodyBgLight: '#efeefd',
        bodyBgDark: '#1d203f'
      })
      this.themeMode(false)
      this.rtlChange(false)
    },
    rtlChange (mode) {
      this.rtl = mode
      // this.modeChange({ rtl: this.rtl, dark: this.dark })
    },
    darkChange (mode) {
      this.dark = mode
      if (mode) {
        this.logo = darkLoader
      } else {
        this.logo = loader
      }
      this.$emit('onLogo', this.logo)
      this.modeChange({ rtl: this.rtl, dark: this.dark })
    },
    // ...mapActions({
    //   modeChange: 'Setting/layoutModeAction'
    // })
  }
}
</script>
