<template>
  <li>
    <a
      v-if="menu.subItems"
      href="javascript:;"
      :class="{
        'side-menu': true,
        'side-menu--active': isChildActive(menu),
      }"
    >
      <div
        class="side-menu__icon"
        v-html="menu.icon ? menu.icon : defaultIcon"
      ></div>
      <div class="side-menu__title">
        {{ menu.title }}
        <div class="side-menu__sub-icon">
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
            class="feather feather-chevron-down"
          >
            <polyline points="6 9 12 15 18 9"></polyline>
          </svg>
        </div>
      </div>
    </a>
    <ul
      v-if="menu.subItems"
      :class="{
        'side-menu__sub-open': isChildActive(menu),
      }"
    >
      <li v-for="(subItem, index) in menu.subItems" :key="index">
        <a
          v-if="subItem.subItems"
          href="javascript:;"
          :class="{
            'side-menu': true,
            'side-menu--active': isChildActive(subItem),
          }"
        >
          <div
            class="side-menu__icon"
            v-html="subItem.icon ? subItem.icon : defaultIcon"
          ></div>
          <div class="side-menu__title">
            {{ subItem.title }}
            <div class="side-menu__sub-icon">
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
                class="feather feather-chevron-down"
              >
                <polyline points="6 9 12 15 18 9"></polyline>
              </svg>
            </div>
          </div>
        </a>
        <ul
          v-if="subItem.subItems"
          :class="{
            'side-menu__sub-open': isChildActive(subItem),
          }"
        >
          <li v-for="(subMenu, z) in subItem.subItems" :key="'subMenu' + z">
            <router-link
              :to="subMenu.path"
              class="ml-2.5"
              :class="{
                'side-menu': true,
                'side-menu--active': isActive(subMenu),
              }"
            >
              <div
                class="side-menu__icon"
                v-html="subMenu.icon ? subMenu.icon : defaultIcon"
              ></div>
              <div class="side-menu__title">{{ subMenu.title }}</div>
            </router-link>
          </li>
        </ul>
        <router-link
          v-else
          :to="subItem.path"
          class="ml-2.5"
          :class="{
            'side-menu': true,
            'side-menu--active': isActive(subItem),
          }"
        >
          <div
            class="side-menu__icon"
            v-html="subItem.icon ? subItem.icon : defaultIcon"
          ></div>
          <div class="side-menu__title">{{ subItem.title }}</div>
        </router-link>
      </li>
    </ul>

    <router-link
      v-else
      :to="menu.path"
      :class="{ 'side-menu': true, 'side-menu--active': isActive(menu) }"
    >
      <div
        class="side-menu__icon"
        v-html="menu.icon ? menu.icon : defaultIcon"
      ></div>
      <div class="side-menu__title">{{ menu.title }}</div>
    </router-link>
  </li>
</template>

<script>
export default {
  props: ["menu"],
  computed: {
    defaultIcon() {
      return `<svg
          xmlns="http://www.w3.org/2000/svg"
          width="24"
          height="24"
          viewBox="0 0 24 24"
          fill="none"
          stroke="currentColor"
          stroke-width="1.5"
          stroke-linecap="round"
          stroke-linejoin="round"
          class="feather feather-box"
        >
          <path
            d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"
          ></path>
          <polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline>
          <line x1="12" y1="22.08" x2="12" y2="12"></line>
        </svg>`;
    },
  },
  methods: {
    // Check menu active
    isActive(menu) {
      if (menu.path === "/") return menu.path === this.$route.path;

      return this.$route.path.startsWith(menu.path);
    },
    // Check active if Sub menu item is active
    isChildActive(menu) {
      for (let child of menu.subItems) {
        if (child.subItems) {
          for (let grand of child.subItems) {
            if (this.$route.path.startsWith(grand.path)) return true;
          }
        } else if (this.$route.path.startsWith(child.path)) {
          return true;
        }
      }
    },
  },
  mounted() {},
};
</script>