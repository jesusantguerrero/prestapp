<script setup lang="ts">
import PrintContainer from "./Partials/PrintTemplate.vue";
import { formatDate, formatMoney } from "@/utils";

interface Props {
  company: Object;
  invoice: Object;
  user: Object;
}

defineProps<Props>();
</script>

<template>
  <PrintContainer ticket>
    <section class="pos-ticket">
      <header class="header justify-center w-full text-center pb-2">
        <div class="logo mx-auto" />
        <div class="info">
          <h2 class="company-oficial-name">{{ company.business_name }}</h2>
          <p class="company-statement">{{ company.description }}</p>
          <p class="company-direction">
            {{
              [
                company.business_street,
                company.business_city,
                company.business_country,
              ].join(", ")
            }}
          </p>
          <p class="company-numbers">{{ company.business_phone }}</p>
          <p></p>
        </div>
      </header>
      <div class="sub-header py-1 overflow-hidden text-sm border-t border-dashed">
        <!-- <h4 class="fecha-recibo">Fecha: {{ formatDate(invoice.payment_date) }}</h4> -->
        <!-- <p>
          <b>Recibo :</b> <span>{{ invoice.id }}</span>
        </p> -->
        <p>
          <b>Cliente : </b><span>{{ invoice.client.display_name }}</span>
        </p>
        <p>
          <b>Email : </b><span>{{ invoice.client.email }}</span>
        </p>
        <p>
          <b>Phone : </b><span>{{ invoice.client.phone }}</span>
        </p>
      </div>
      <main class="main">
        <div
          class="concept border-y border-dashed py-2 text-center uppercase text-xs font-bold"
        >
          <h4>{{ invoice.concept }}</h4>
        </div>
        <table class="w-full">
          <thead class="border-b border-dashed text-sm">
            <th class="py-1 text-left">Descripcion</th>
            <th class="py-1">Valor</th>
          </thead>
          <tbody class="text-sm">
            <tr class="font-bold">
              <td class="pt-2"></td>
              <td class="pt-2"></td>
            </tr>
            <tr v-for="line in invoice.lines">
              <td>{{ line.concept }}</td>
              <td class="text-right">{{ formatMoney(line.amount) }}</td>
            </tr>
            <template v-if="invoice.discounts.length">
              <tr class="font-bold">
                <td class="pt-2 font-semibold">Descuentos</td>
                <td class="pt-2"></td>
              </tr>
              <tr v-for="line in invoice.discounts">
                <td>{{ line.concept }}</td>
                <td class="text-right">{{ formatMoney(line.amount) }}</td>
              </tr>
            </template>
            <tr class="font-bold">
              <td class="pt-4">Total a pagar</td>
              <td class="pt-4">{{ formatMoney(invoice.total) }}</td>
            </tr>
          </tbody>
        </table>
      </main>
      <footer class="footer border-t border-dashed py-1">
        <small>**Verifique su recibo valor no reembolsable**</small>
      </footer>
    </section>
  </PrintContainer>
</template>

<style lang="scss">
@mixin size($w, $h) {
  width: $w;
  height: $h;
}

@mixin makeFlex($width: 100%, $direction: row, $justify: flex-start, $align: flex-start) {
  width: $width;
  display: flex;
  flex-direction: $direction;
  justify-content: $justify;
  align-items: $align;
}

.header {
  min-height: 100px;
  .logo {
    height: 60px;
    width: 60px;
    background: url(http://michaeltruong.ca/images/logo1.png) no-repeat;
    background-size: 60px 60px;
  }
}

.main {
  min-height: 80px;
}
.footer {
  min-height: 50px;
}

.pos-ticket {
  box-shadow: 0 0 1in -0.25in rgba(0, 0, 0, 0.5);
  padding: 2mm;
  margin: 0 auto;
  width: 79mm;
  background: #fff;
}
.header {
  p {
    padding: 0 0 0 0;
    margin: 0 0 0 0;
    font-size: 12px;
  }
  .company-name {
    width: 60%;
    h2 {
      padding-bottom: 0;
      margin-bottom: 0;
      font-weight: bolder;
      color: #f20;
    }
  }
}
.concepto h4 {
  text-align: center;
  font-weight: bolder;
  color: #666;
  padding: 5px 0;
  background: whitesmoke;
  margin: 0 0 0 0;
}
.fecha-recibo {
  color: #06f !important;
}
.logo-recibo {
  width: 8%;
  margin: 2%;
}
.left-box {
  align-self: center;
  font-size: 12px;
  margin: 20px;
}
.cuerpo {
  padding: 10px 15px;
  margin: 10px;
  margin-bottom: 3px;
  border-radius: 4px;
}
.text-placeholder {
  border-bottom: 1.4px dashed #ccc !important;
  display: inline-block;
  padding: 0;
  margin: 0;
  @include size(82%, 20px);
  &.md {
    width: 18.43%;
  }
  &.lg {
    width: 50%;
  }
}
.text-main {
  display: inline-block;
  @include size(12%, 20px);
  font-weight: 600;
  color: #333 !important;
  &.center {
    text-align: center;
  }
}
.line {
  @include makeFlex(100%, row, null, center);
}
.pie-pagina {
  text-align: center;
}
@media print {
  .pos-ticket {
    width: 100%;
    border: none;
    overflow: hidden;
    border-bottom: 2px dashed #ddd;
    border-left: 1px dashed #ddd;
    border-right: 1px dashed #ddd;
    -webkit-print-color-adjust: exact;
  }
  .fecha-recibo {
    color: #06f !important;
  }
  .concepto h4 {
    background: whitesmoke !important;
  }
  .company-name h2 {
    color: #f20 !important;
  }
  .cuerpo {
    padding: 10px 15px;
    margin-bottom: 0;
  }
  p.text-placeholder {
    border-bottom: 2px solid #aaa !important;
  }
  @page {
    marks: cross;
    margin-top: 0 !important;
    margin-bottom: 0 !important;
  }

  #main {
    margin: 100px 0 !important;
  }
} ;
</style>
