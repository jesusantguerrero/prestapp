<script setup lang="ts">
import AppLayout from "@/Components/templates/AppLayout.vue";

defineProps<{
  user: Object;
}>();
</script>

<template>
  <AppLayout title="Ayuda y soporte">
    <section class="w-full md:space-x-4 pb-56">
      <h1>Hola {{ user.name }} Bienvenido a ICLoan!</h1>
      <article class="mt-4">
        <h4 class="text-primary font-bold text-4xl">Introduccion a ICLoan</h4>
        <div class="rounded-md overflow-hidden mt-4">
          <iframe
            width="640"
            height="360"
            src="https://www.loom.com/embed/e478000c568e41ba8eedfd671c2f33f9"
            frameborder="0"
            webkitallowfullscreen
            mozallowfullscreen
            allowfullscreen
          ></iframe>
        </div>
      </article>

      <article class="mt-16">
        <h4 class="text-secondary font-bold text-2xl">
          Explora las caracteristicas principales del sistema
        </h4>
        <p>Aprende a usar las secciones basicas del sistema</p>
        <section class="flex mt-4 space-x-4">
          <article class="w-full">
            <div style="position: relative; padding-bottom: 56.25%; height: 0">
              <iframe
                src="https://www.loom.com/embed/0e00c112185a414a98f9e2a5a5b51186"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%"
              ></iframe>
            </div>
          </article>
          <article class="w-full">
            <div style="position: relative; padding-bottom: 56.25%; height: 0">
              <iframe
                src="https://www.loom.com/embed/b93f9fa411194888b5e4e5d5c693c04c"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%"
              ></iframe>
            </div>
          </article>
          <article class="w-full">
            <div style="position: relative; padding-bottom: 56.25%; height: 0">
              <iframe
                src="https://www.loom.com/embed/943b7755ada7486a9ec64c2fb74618ff"
                frameborder="0"
                webkitallowfullscreen
                mozallowfullscreen
                allowfullscreen
                style="position: absolute; top: 0; left: 0; width: 100%; height: 100%"
              ></iframe>
            </div>
          </article>
        </section>
      </article>
    </section>
  </AppLayout>
</template>
