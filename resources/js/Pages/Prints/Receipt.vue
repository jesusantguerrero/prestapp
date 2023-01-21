<script setup lang="ts">
import PrintContainer from "./Partials/PrintTemplate.vue";
import { formatDate, formatMoney } from "@/utils";

interface Props {
  company: Object;
  receipt: Object;
  user: Object;
}

defineProps<Props>();
</script>

<template>
  <PrintContainer>
    <section class="recibo-body">
      <div class="cabecera">
        <img class="logo-recibo" :src="`/assets/uploads/${company.logo}`" alt="" />
        <div class="company-name">
          <h2 class="company-oficial-name">{{ company.business_name }}</h2>
          <p class="company-statement">{{ company.description }}</p>
          <p class="company-direction">{{ company.business_street }}</p>
          <p class="company-numbers">{{ company.business_phone }}</p>

          <p></p>
        </div>
        <div class="left-box">
          <h4 class="fecha-recibo">Fecha: {{ formatDate(receipt.payment_date) }}</h4>
          <p>
            <b>Recibo :</b> <span>{{ receipt.id }}</span>
          </p>
          <p>
            <b>{{ receipt.resource_name }}: </b><span>{{ receipt.resource_id }}</span>
          </p>
          <p>
            <b>Cliente : </b><span>{{ receipt.client_name }}</span>
          </p>
        </div>
      </div>
      <div class="concepto">
        <h4>{{ receipt.concept }}</h4>
      </div>
      <div class="cuerpo">
        <p class="line">
          <span class="text-main">Detalle:</span>
          <span class="text-placeholder">{{ receipt.description }}</span>
        </p>
        <p class="line">
          <span class="text-main">Suma:</span>
          <span class="text-placeholder uppercase"
            >{{ receipt.total_in_words }} PESOS</span
          >
        </p>
        <p>
          <span class="text-main">Mensualidad:</span>
          <span class="text-placeholder md text-right">
            {{ formatMoney(receipt.amount) }}</span
          >
          <span class="text-main center">Mora:</span>
          <span class="text-placeholder md text-right">
            {{ formatMoney(receipt.late_fee) }}
          </span>
          <span class="text-main center">Extras</span>
          <span class="text-placeholder md text-right">
            {{ formatMoney(receipt.extra) }}</span
          >
        </p>
        <template v-if="receipt.discount">
          <p class="line">
            <span class="text-main">Descuento:</span>
            <span class="text-placeholder"
              ><i>{{ formatMoney(receipt.discount) }}</i></span
            >
          </p>
          <p class="line">
            <span class="text-main">Por:</span>
            <span class="text-placeholder">{{ receipt.discount_description }}</span>
          </p>
        </template>
        <p>
          <span class="text-main">Recibe:</span>
          <span class="text-placeholder lg">{{ user.name }}</span>
          <span class="text-main center">Total:</span>
          <span class="text-placeholder md text-right">
            {{ formatMoney(receipt.amount) }}</span
          >
        </p>
      </div>
      <div class="pie-pagina">
        <small>**Verifique su recibo valor no reembolsable**</small>
      </div>
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

.recibo-body {
  width: 210mm;
  height: auto;
  min-height: 350px;
  border: 2px dashed #aaa;
  overflow: hidden;
  font-family: "Times New Roman", Times, serif;
  background: #fff;
}
.cabecera {
  @include makeFlex(100%, row, nullnull);
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
  .recibo-body {
    width: 210mm;
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
