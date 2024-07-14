<template>
  <div class="container mt-5">
    <h2>Configuración de Tarifas</h2>
    <form @submit.prevent="updateSettings">
      <div class="form-group">
        <label for="gasolinePrice">Precio de Gasolina (por 100 km)</label>
        <input type="number" id="gasolinePrice" v-model.number="settings.gasolinePrice" class="form-control">
      </div>
      <div class="form-group">
        <label for="vanPrice">Precio de la Furgoneta</label>
        <input type="number" id="vanPrice" v-model.number="settings.vanPrice" class="form-control">
      </div>
      <div class="form-group">
        <label for="driverPrice">Precio del Conductor (por hora)</label>
        <input type="number" id="driverPrice" v-model.number="settings.driverPrice" class="form-control">
      </div>
      <div class="form-group">
        <label for="assistantPrice">Precio del Ayudante (por hora)</label>
        <input type="number" id="assistantPrice" v-model.number="settings.assistantPrice" class="form-control">
      </div>
      <div class="form-group">
        <label for="commissionRate">Comisión (%)</label>
        <input type="number" id="commissionRate" v-model.number="settings.commissionRate" class="form-control">
      </div>
      <button type="submit" class="btn btn-primary mt-3">Actualizar</button>
    </form>
  </div>
</template>

<script>
export default {
  data() {
    return {
      settings: {
        gasolinePrice: 1.5,
        vanPrice: 20,
        driverPrice: 15,
        assistantPrice: 10,
        commissionRate: 10,
      }
    };
  },
  methods: {
    updateSettings() {
      // Guardar los ajustes en una base de datos o un estado global
      console.log('Ajustes actualizados:', this.settings);
      // Aquí podrías hacer una llamada a una API para guardar los ajustes en la base de datos
    }
  }
};
</script>

<style scoped>
.container {
  max-width: 600px;
  margin: 0 auto;
}
</style>
