<template>
  <div
    class="bg-white overflow-hidden shadow-xl sm:rounded-lg rounded-md shadow-md divide-x-2 flex border"
  >
    <article class="w-9/12 p-5">
      <header>
        <h4 class="font-bold">Total Income</h4>
        <small>Yearly report income</small>
      </header>
    </article>
    <article class="w-3/12 p-5">
      <header>
        <h4 class="font-bold">Report</h4>
        <small>Monthly Avg.</small>
      </header>
      <section class="space-y-2 mt-4">
        <div v-for="items in [1, 2, 3]" class="bg-gray-100 p-2 rounded-md">
          <h4>Income</h4>
          <p class="font-bold">42,845.00</p>
        </div>
      </section>
    </article>
  </div>
</template>
