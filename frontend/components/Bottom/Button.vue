<script setup lang="ts">
const props = defineProps({
  to: {
    type: String,
    required: true
  },
  text: {
    type: String,
    required: true
  }
})

const isSelected = ref<boolean>(false);

const route = useRoute();

onMounted(() => {
  if(route.path === props.to) {
    isSelected.value = true;
  }
})
</script>

<template>
<NuxtLink :to="to" :class="['flex flex-col items-center w-fit', isSelected ? 'text-green-500' : '']"><slot/> {{ text }} </NuxtLink>
</template>

<style scoped>

</style>