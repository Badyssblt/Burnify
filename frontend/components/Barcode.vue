<template>
  <div class="scanner-container">
    <div v-show="!isLoading">
      <video poster="data:image/gif,AAAA" ref="scanner"></video>
      <div class="overlay-element"></div>
      <div class="laser"></div>
    </div>
  </div>
</template>

<script setup>
import { BrowserMultiFormatReader, Exception } from "@zxing/library";

const isLoading = ref(true);
const scanner = ref(null);
const codeReader = new BrowserMultiFormatReader();
const barcode = ref(0)
const model = defineModel();
const emits = defineEmits(['scanSuccess']);

const isMediaStreamAPISupported = navigator && navigator.mediaDevices && "enumerateDevices" in navigator.mediaDevices;

if (!isMediaStreamAPISupported) {
  throw new Exception("Media Stream API is not supported");
}

const food = ref(null)

const start =  () => {
  codeReader.decodeFromVideoDevice(undefined, scanner.value,  (result, err) => {
    if (result) {
      barcode.value = result;
      model.value = result
      emits('scanSuccess')
    }
  });
};


onMounted(() => {
  start();

  if (scanner.value) {
    scanner.value.oncanplay = () => {
      isLoading.value = false;
      const loadedEvent = new CustomEvent('loaded');
      scanner.value.dispatchEvent(loadedEvent);
    };
  }
});

onBeforeUnmount(() => {
  codeReader.reset();
});
</script>

<style scoped>
video {
  max-width: 100%;
  max-height: 100%;
}
.scanner-container {
  position: relative;
}

.overlay-element {
  position: absolute;
  top: 0;
  width: 100%;
  height: 99%;
  background: rgba(30, 30, 30, 0.5);

  -webkit-clip-path: polygon(0% 0%, 0% 100%, 20% 100%, 20% 20%, 80% 20%, 80% 80%, 20% 80%, 20% 100%, 100% 100%, 100% 0%);
  clip-path: polygon(0% 0%, 0% 100%, 20% 100%, 20% 20%, 80% 20%, 80% 80%, 20% 80%, 20% 100%, 100% 100%, 100% 0%);
}

.laser {
  width: 60%;
  margin-left: 20%;
  background-color: tomato;
  height: 1px;
  position: absolute;
  top: 40%;
  z-index: 2;
  box-shadow: 0 0 4px red;
  -webkit-animation: scanning 2s infinite;
  animation: scanning 2s infinite;
}
@-webkit-keyframes scanning {
  50% {
    -webkit-transform: translateY(75px);
    transform: translateY(75px);
  }
}
@keyframes scanning {
  50% {
    -webkit-transform: translateY(75px);
    transform: translateY(75px);
  }
}
</style>
