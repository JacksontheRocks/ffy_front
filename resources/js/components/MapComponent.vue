<template>
  <div>
    <h1>Calculate Route</h1>
    <input type="text" v-model="origin" placeholder="Enter origin" />
    <input type="text" v-model="destination" placeholder="Enter destination" />
    <button @click="calculateRoute">Calculate</button>
    <div id="map" style="height: 500px;"></div>
    <div v-if="distance && duration">
      <p><strong>Distance:</strong> {{ distance }}</p>
      <p><strong>Estimated Time:</strong> {{ duration }}</p>
      <p><strong>Tariff:</strong> ${{ tariff }}</p>
    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      origin: '',
      destination: '',
      distance: null,
      duration: null,
      tariff: null,
      map: null,
      directionsService: null,
      directionsRenderer: null,
    };
  },
  mounted() {
    this.initMap();
  },
  methods: {
    initMap() {
      this.map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 8,
      });
      this.directionsService = new google.maps.DirectionsService();
      this.directionsRenderer = new google.maps.DirectionsRenderer();
      this.directionsRenderer.setMap(this.map);
    },
    calculateRoute() {
      const request = {
        origin: this.origin,
        destination: this.destination,
        travelMode: 'DRIVING',
      };
      this.directionsService.route(request, (result, status) => {
        if (status === 'OK') {
          this.directionsRenderer.setDirections(result);
          const route = result.routes[0].legs[0];
          this.distance = route.distance.text;
          this.duration = route.duration.text;
          const distanceInKm = route.distance.value / 1000;
          this.tariff = (2.00 + (1.50 * distanceInKm)).toFixed(2);
        }
      });
    },
  },
};
</script>

<style scoped>
#map {
  width: 100%;
}
</style>
