<template>
  <!-- BEGIN: Top Bar -->
  <div class="top-bar">
    <!-- BEGIN: Breadcrumb -->
    <div class="-intro-x breadcrumb mr-auto hidden sm:flex">
        <a href="">Application</a>
        <i data-feather="chevron-right" class="breadcrumb__icon"></i>
        <a href="" class="breadcrumb--active">Dashboard</a>
    </div>
    <!-- END: Breadcrumb -->
    <!-- BEGIN: Exchange Rate -->
    <div class="intro-x relative mr-3 sm:mr-6 flex mb-1">
        <span class="mt-1 font-bold text-base text-red-700">EXCHANGE RATE:</span>
        <span class="mt-1 ml-2 font-bold text-base">1 USD =</span>
        <span class="mt-1 ml-1 mr-3 font-bold text-base">{{ exchangeRateInfo.mid_rate }} KHR</span>
        <img class="mt-2 mr-1 w-5 h-5" src="/images/exchange_rate/calendar.png" />
        <span class="mt-1 font-bold text-base">{{ exchangeRateInfo.rate_date }}</span>
    </div>
    <!-- END: Exchange Rate -->
    <!-- BEGIN: Search -->
    <div class="intro-x relative mr-3 sm:mr-6">
        <div class="search hidden sm:block">
            <input
            type="text"
            class="
                search__input
                form-control
                border-transparent
                placeholder-theme-13
            "
            placeholder="Search..."
            />
            <i data-feather="search" class="search__icon dark:text-gray-300"></i>
        </div>
        <a class="notification sm:hidden" href="">
            <i
            data-feather="search"
            class="notification__icon dark:text-gray-300"
            ></i>
        </a>
    </div>
    <!-- END: Search -->
    <!-- BEGIN: Account Menu -->
    <div class="intro-x dropdown w-8 h-8">
        <div
            class="
            dropdown-toggle
            w-8
            h-8
            rounded-full
            overflow-hidden
            shadow-lg
            image-fit
            zoom-in
            "
            role="button"
            aria-expanded="false"
        >
            <img :alt="full_name" src="/images/profile-5.jpg" />
        </div>
        <div class="dropdown-menu w-56">
            <div class="dropdown-menu__content box bg-theme-26 dark:bg-dark-6">
                <div class="p-4 border-b border-theme-27 dark:border-dark-3">
                <div class="font-medium">{{ full_name }}</div>
            </div>
            <div class="p-2 border-t border-theme-27 dark:border-dark-3">
                <router-link
                    :to="{ name: 'ChangePasswordForm' }"
                    class="
                        flex
                        items-center
                        p-2
                        transition
                        duration-300
                        ease-in-out
                        rounded-md
                        cursor-pointer
                    "
                >
                    <svg
                        xmlns="http://www.w3.org/2000/svg"
                        width="24"
                        height="24"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="1.5"
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        class="feather feather-lock w-4 h-4 mr-2"
                    >
                        <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                        <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                    </svg>
                    Change Password
                </router-link>
                <a class="hidden" href="/logout" id="link-logout"></a>
                <a
                    @click="logout"
                    class="
                        flex
                        items-center
                        p-2
                        transition
                        duration-300
                        ease-in-out
                        rounded-md
                        cursor-pointer
                    "
                >
                    <i data-feather="toggle-right" class="w-4 h-4 mr-2"></i> Logout
                </a>
            </div>
        </div>
      </div>
    </div>
    <!-- END: Account Menu -->
  </div>
</template>

<script>
import axios from "axios";

export default {
    props: {
        exchangeRateInfo: Object,
    },
    data() {
        return {
            full_name: "",
        };
    },
    methods: {
        logout() {
            document.getElementById("link-logout").click();
        },
    },
    created() {
        axios.get("/logged-in-info").then((response) => {
            this.full_name = response.data.full_name;
        });
    },
};
</script>
