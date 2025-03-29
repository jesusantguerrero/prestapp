<template>
  <main
    class="min-h-screen bg-base-lvl-3 home-container"
    :class="{ expanded: isExpanded }"
  >
    <nav
      class="app-header bg-primary md:bg-neutral primary border-b"
      :class="[navClass, $slots.aside && 'with-nav']"
    >
      <slot name="navigation" />
    </nav>

    <article class="app-content" :class="{'with-nav': $slots.aside }">
      <aside class="app-side-container" v-if="$slots.aside">
        <slot name="aside" />
      </aside>

      <section class="border app-content__inner ic-scroller bg-base-100">
        <slot name="main-section" />
      </section>
    </article>
  </main>
</template>

<script lang="ts" setup>
defineProps({
  navClass: {
    type: [String, Object],
  },
  isExpanded: {
    type: Boolean,
    default: true,
  },
});
</script>

<style lang="scss">
.home-container {
  position: relative;
  height: 100vh;

  --app-side-width: 74px;

  &.expanded {
    --app-side-width: 230px;
  }
}

.app-side {
  grid-template-columns: var(--app-side-width);
}

.app-header {
  width: 100%;
  top: 0;
  position: fixed;
  z-index: 1000;
}

@screen lg {
  .app-header {
    padding-left: 0;
    &.with-nav {
      padding-left: var(--app-side-width);
    }
  }
}

.app-side-container {
  padding-right: 0 !important;
  position: fixed;
  display: grid;
  width: var(--app-side-width);
  height: 100%;
  z-index: 1001;
}

.app-content {
  display: flex;
  position: relative;
  height: 100vh;
  transition: all ease 0.3s;

  &.with-nav {
    display: grid !important;
    grid-template-columns: var(--app-side-width) minmax(0, 1fr);
  }

  &__inner {
    width: 100%;
    grid-column-start: 2;
    padding: 64px 0;
    padding-top: 63px;
    padding-bottom: 0;
    position: relative;
    max-height: 100%;
    transition: all ease 0.3s;

    &.header-replacer-mode {
      padding-top: 0;

      .header-replacer {
        height: 73px;
        margin: 0;
        position: fixed;
        left: 0;
        top: 0;
        display: flex;
        width: 100%;
        z-index: 1000;
        background: white;
        align-items: center;
        padding: 0 10px;
      }

      .section-body {
        padding-top: 140px;
      }
    }
  }
}

.splash-screen {
  background: dodgerblue;
  width: 100%;
  height: 100vh;
  color: white;
  display: flex;
  align-items: center;
  justify-content: center;
}

@media screen and (max-width: 992px) {
  .app-side-container {
    z-index: 999;
    width: 256px;
    left: -260px;
    transition: all ease 0.3s;
  }

  .app-content {
    height: auto;
    &__inner {
      grid-column-start: 1;
      grid-column-end: 3;
      padding-bottom: 40px;
    }
  }

  .home-container.menu-expanded {
    .app-side-container {
      left: 0;
    }
  }
}

@media print {
  .app-side-container,
  .no-print,
  button {
    display: none;
  }

  table {
    width: 100% !important;
    overflow: hidden;
  }

  th td {
    overflow: hidden;
  }

  .app-content {
    grid-column-start: 1;
    grid-column-end: 3;
  }
}

.ic-scroller {
  &::-webkit-scrollbar-thumb {
    background-color: rgba(0, 0, 0, 0.3);
    border-radius: 4px;

    &:hover {
      background-color: rgba(0, 0, 0, 0.7);
    }
  }

  &::-webkit-scrollbar {
    background-color: transparent;
    width: 8px;
    height: 10px;
  }

  &-slim {
    transition: all ease 0.3s;
    &::-webkit-scrollbar {
      height: 0;
    }

    &:hover {
      &::-webkit-scrollbar {
        height: 3px;
      }
    }
  }
}
</style>
