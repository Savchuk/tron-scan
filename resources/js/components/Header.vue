<template>
    <div class="uk-section-primary uk-preserve-color">

        <div
            uk-sticky="animation: uk-animation-slide-top; sel-target: .uk-navbar-container; cls-active: uk-navbar-sticky; cls-inactive: uk-navbar-transparent uk-light; top: 200">

            <nav class="uk-navbar-container">
                <div class="uk-container uk-container-expand">
                    <div uk-navbar>

                        <div class="uk-navbar-left">
                            <a href="/" class="uk-navbar-item uk-logo">
                                <img uk-svg="" src="https://getuikit.com/images/uikit-logo.svg"
                                     class="uk-margin-small-right">
                            </a>
                        </div>

                        <ul class="uk-navbar-nav">
                            <li v-for="link in links">
                                <router-link :to="link.href">{{ link.title }}</router-link>
                            </li>
                        </ul>

                    </div>
                </div>
            </nav>
        </div>

        <div class="uk-section uk-light">
            <div class="uk-container">

                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</p>

                <p>Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum. Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris.</p>

                <p>Lorem ipsum dolor sit amet, consectetur adipiscing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum.</p>

            </div>
        </div>

    </div>
</template>

<script>
export default {
    data () {
        return {
            links: [
                {
                    title: "Главная",
                    href: "/"
                },
                {
                    title: "Процессы",
                    href: "/process"
                },
                {
                    title: "Аналитика",
                    href: "/analytics"
                },
                {
                    title: "Настройки",
                    href: "/settings"
                },
            ]
        }

    }
    // mounted() {
    //     console.log('Component mounted.')
    // }
}
</script>
