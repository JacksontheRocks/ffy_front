<template>
  <div class="d-flex justify-content-center align-items-center min-vh-100">
    <div class="card p-4 text-center">
      <div class="form-group mb-3">
        <label for="pickup">Punto de Recogida</label>
        <input type="text" id="pickup" v-model="pickup" class="form-control" placeholder="Ingrese punto de recogida">
      </div>
      <div class="form-group mb-3">
        <label for="dropoff">Punto de Entrega</label>
        <input type="text" id="dropoff" v-model="dropoff" class="form-control" placeholder="Ingrese punto de entrega">
      </div>
      <div v-if="!fare" class="mb-3">
        <button @click="calculateRoute" class="btn btn-primary">Calcular Ruta</button>
      </div>
      <div id="map" class="mt-3"></div>
      <div v-if="distance && duration" class="mt-2 text-muted small">
        <p>Kilómetros: {{ distance }} | Tiempo: {{ duration }}</p>
      </div>
      <div v-if="fare" class="mt-2 text-muted small">
        <p>Tarifa Provisional: {{ fare.toFixed(2) }} €</p>
      </div>
      <div v-if="fare" class="mt-2 text-muted small text-center">
        <p>El conductor ni carga ni descarga. ¿Cuánto tiempo aproximado cree que se tardará en cargar?</p>
        <input type="number" v-model.number="loadingTime" class="form-control" min="0" placeholder="Tiempo en minutos" @change="calculateFareAfterLoading">
      </div>
      <div v-if="newFare" class="mt-2 text-muted small">
        <p>Nueva Tarifa Provisional: {{ newFare.toFixed(2) }} €</p>
        <p>IVA (21%): {{ iva.toFixed(2) }} €</p>
        <p>Tarifa Total con IVA: {{ totalFare.toFixed(2) }} €</p>
      </div>
      <div v-if="newFare" class="mt-2 text-muted small text-center">
        <p>Esta tarifa es orientativa hasta que sepamos el tiempo real de carga/descarga. Cuánto más preparado tengas todo para cargar más barato será el porte.</p>
      </div>
      <div v-if="newFare" class="mt-3">
        <button @click="showOptions = !showOptions" class="btn btn-success">SOLICITAR PORTE</button>
      </div>
      <div v-if="showOptions" class="mt-3">
        <p>¿Cuándo deseas realizar el porte?</p>
        <button @click="requestPorte('urgent')" class="btn btn-outline-primary me-2">CUÁNTO ANTES</button>
        <button @click="showSchedule = !showSchedule" class="btn btn-outline-primary">PROGRAMAR PORTE</button>
      </div>
      <div v-if="showSchedule" class="mt-3">
        <label for="scheduledDate">Fecha y Hora</label>
        <input type="datetime-local" v-model="scheduledDate" class="form-control" id="scheduledDate">
        <button @click="requestPorte('scheduled')" class="btn btn-primary mt-3">Confirmar</button>
      </div>
      <div v-if="errorMessage" class="text-danger mt-2">{{ errorMessage }}</div>
      <div class="loading-spinner" v-if="isLoading">
        <div class="spinner-border" role="status">
          <span class="visually-hidden">Loading...</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { getDistance } from 'geolib';

export default {
  data() {
    return {
      pickup: '',
      dropoff: '',
      loadingTime: 0,
      map: null,
      directionsService: null,
      directionsRenderer: null,
      geocoder: null,
      userLocation: null,
      mapId: import.meta.env.VITE_GOOGLE_MAP_ID,
      distance: null,
      duration: null,
      fare: null,
      newFare: null,
      gasolineCost: null,
      additionalDriverCost: null,
      additionalVanCost: null,
      commission: null,
      iva: null,
      totalFare: null,
      errorMessage: '',
      showOptions: false,
      showSchedule: false,
      scheduledDate: '',
      km0: { lat: 40.416775, lng: -3.703790 },
      isLoading: false,
    };
  },
  props: {
    settings: {
      type: Object,
      required: true,
    }
  },
  mounted() {
    this.loadGoogleMaps();
    this.setSettings();
  },
  methods: {
    loadGoogleMaps() {
      if (typeof google === 'undefined') {
        const script = document.createElement('script');
        script.src = `https://maps.googleapis.com/maps/api/js?key=${import.meta.env.VITE_GOOGLE_MAPS_API_KEY}&libraries=places,marker&callback=initMap`;
        script.async = true;
        script.defer = true;
        script.id = 'googleMaps';
        window.initMap = this.initMap.bind(this);
        document.body.appendChild(script);
      } else {
        this.initMap();
      }
    },
    initMap() {
      this.map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: -34.397, lng: 150.644 },
        zoom: 8,
        mapId: this.mapId,
      });
      this.directionsService = new google.maps.DirectionsService();
      this.directionsRenderer = new google.maps.DirectionsRenderer();
      this.directionsRenderer.setMap(this.map);
      this.geocoder = new google.maps.Geocoder();
      this.initializeAutocomplete();
      this.getUserLocation();
    },
    initializeAutocomplete() {
      const pickupInput = document.getElementById('pickup');
      const dropoffInput = document.getElementById('dropoff');
      const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput);
      const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput);
      pickupAutocomplete.setFields(['place_id', 'geometry', 'name']);
      dropoffAutocomplete.setFields(['place_id', 'geometry', 'name']);
      
      pickupAutocomplete.addListener('place_changed', () => {
        const place = pickupAutocomplete.getPlace();
        if (!place.geometry) {
          console.error("Autocomplete's returned place contains no geometry");
          return;
        }
        this.pickup = place.name;
      });

      dropoffAutocomplete.addListener('place_changed', () => {
        const place = dropoffAutocomplete.getPlace();
        if (!place.geometry) {
          console.error("Autocomplete's returned place contains no geometry");
          return;
        }
        this.dropoff = place.name;
      });
    },
    getUserLocation() {
      if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(position => {
          const pos = {
            lat: position.coords.latitude,
            lng: position.coords.longitude,
          };
          this.userLocation = pos;
          this.map.setCenter(pos);
          new google.maps.marker.AdvancedMarkerElement({
            position: pos,
            map: this.map,
            title: 'Tu ubicación',
          });
        });
      } else {
        console.error("El navegador no soporta geolocalización");
      }
    },
    validatePickupLocation(location) {
      const distance = getDistance(
        { latitude: location.lat, longitude: location.lng },
        { latitude: this.km0.lat, longitude: this.km0.lng }
      );
      return distance <= 120000; // 120 km en metros
    },
    calculateRoute() {
      if (!this.pickup || !this.dropoff) {
        this.errorMessage = 'Por favor, ingrese ambos puntos de recogida y entrega';
        return;
      }

      this.geocoder.geocode({ address: this.pickup }, (results, status) => {
        if (status === 'OK') {
          const pickupLocation = results[0].geometry.location;
          if (!this.validatePickupLocation({ lat: pickupLocation.lat(), lng: pickupLocation.lng() })) {
            this.errorMessage = 'Lo siento pero solo podemos cubrir la Comunidad de Madrid';
            return;
          }
          this.errorMessage = '';

          const request = {
            origin: this.pickup,
            destination: this.dropoff,
            travelMode: 'DRIVING',
          };
          this.directionsService.route(request, (result, status) => {
            if (status === 'OK') {
              this.directionsRenderer.setDirections(result);
              const route = result.routes[0].legs[0];
              this.distance = route.distance.text;
              this.duration = route.duration.text;
              this.calculateFare(route.distance.value, route.duration.value);
            } else {
              this.errorMessage = "No se pudo calcular la ruta: " + status;
            }
          });
        } else if (status === 'ZERO_RESULTS') {
          this.errorMessage = 'No se encontraron resultados para la dirección proporcionada. Por favor, verifica la dirección e intenta nuevamente.';
        } else {
          this.errorMessage = 'Geocode no fue exitoso por la siguiente razón: ' + status;
        }
      });
    },
    calculateFare(distance, duration) {
      const distanceKm = distance / 1000;
      const durationMinutes = duration / 60;

      this.gasolineCost = (this.settings.gasoline_price / 100) * distanceKm;
      const vanCost = (this.settings.van_price / 60) * durationMinutes;
      const driverCost = (this.settings.driver_price / 60) * durationMinutes;

      const subtotal = this.gasolineCost + vanCost + driverCost;
      const commission = subtotal * (this.settings.commission_rate / 100);
      this.fare = subtotal + commission;
    },
    calculateFareAfterLoading() {
      const additionalMinutes = this.loadingTime * 2; // Carga y descarga
      const additionalHours = additionalMinutes / 60;
      this.additionalDriverCost = this.settings.driver_price * additionalHours;
      this.additionalVanCost = this.settings.van_price * additionalHours;

      this.newFare = this.fare + this.additionalDriverCost + this.additionalVanCost;
      this.commission = this.newFare * (this.settings.commission_rate / 100);
      this.newFare += this.commission;
      this.iva = this.newFare * 0.21;
      this.totalFare = this.newFare + this.iva;
    },
    setSettings() {
      this.gasolinePrice = this.settings.gasoline_price;
      this.vanPrice = this.settings.van_price;
      this.driverPrice = this.settings.driver_price;
      this.assistantPrice = 0; // Eliminamos el costo de ayudantes
      this.commissionRate = this.settings.commission_rate / 100;
    },
    requestPorte(type) {
      const requestData = {
        pickup_location: this.pickup,
        dropoff_location: this.dropoff,
        pickup_time: this.scheduledDate || new Date().toISOString(),
        dropoff_time: this.scheduledDate || new Date().toISOString(),
        fare: this.totalFare,
      };

      if (type === 'urgent') {
        this.sendRequestPorte(requestData);
      } else if (type === 'scheduled' && this.scheduledDate) {
        this.sendRequestPorte(requestData);
      } else {
        alert('Por favor, selecciona una fecha y hora para programar el porte.');
      }
    },
    async sendRequestPorte(data) {
      this.isLoading = true;
      try {
        const response = await fetch('/create_order', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
          },
          body: JSON.stringify(data)
        });

        if (!response.ok) {
          throw new Error('Error en la solicitud');
        }

        const result = await response.json();
        this.isLoading = false;
        alert('Pedido creado exitosamente');
        // Puedes redirigir a otra página si es necesario
        // window.location.href = "/ruta-deseada";
      } catch (error) {
        this.isLoading = false;
        this.errorMessage = 'Hubo un error al crear el pedido: ' + error.message;
      }
    }
  },
};
</script>

<style scoped>
#map {
  width: 100%;
  height: 300px;
}
.d-flex {
  display: flex;
}
.justify-content-center {
  justify-content: center;
}
.align-items-center {
  align-items: center;
}
.min-vh-100 {
  min-height: 100vh;
}
.card {
  width: 100%;
  max-width: 600px;
}
.text-muted {
  color: #6c757d !important;
}
.small {
  font-size: 0.875em;
}
.text-danger {
  color: #dc3545 !important;
}
.loading-spinner {
  position: fixed;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  display: flex;
  justify-content: center;
  align-items: center;
  background-color: rgba(255, 255, 255, 0.75);
  z-index: 9999;
}
</style>
