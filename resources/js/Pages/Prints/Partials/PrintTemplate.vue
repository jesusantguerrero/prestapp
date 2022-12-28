<template>
  <main class="print-container text-body">
    <slot />
  </main>
</template>

<style>
.print-container,
body {
  width: 100%;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background: #222;
}
</style>
