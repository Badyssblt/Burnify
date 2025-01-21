<script setup lang="ts">

const { $api } = useNuxtApp();

const caloryLeft = ref(0);




const props = defineProps({
  meals: {
    type: Object,
    required: false
  },
  user: {
    type: Object,
    required: true
  },
  overload: {
    type: Boolean,
    required: false
  }
})


const progress = computed(() => {
  if (!props.user) return 0;
  const totalCalories = props.user.caloryPerDay || 0;
  const remainingCalories = calculateCaloryLeft();

  const percentage = Math.max(0, Math.min(remainingCalories / totalCalories, 1));

  return (1 - percentage) * 75;
});



const calculateCaloryLeft = () => {
  return props.user.caloryPerDay - props.meals[1];
}

</script>

<template>
  <div class="relative size-28">
    <svg class="rotate-[135deg] size-full" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
      <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-200 dark:text-neutral-700" stroke-width="1.5" stroke-dasharray="75 100" stroke-linecap="round"></circle>

      <circle cx="18" cy="18" r="16" fill="none" :class="['stroke-current', overload ? 'text-red-500' : 'text-green-500']" stroke-width="1.5" :stroke-dasharray="`${progress} 100`" stroke-linecap="round"></circle>
    </svg>

    <div class="absolute top-1/2 start-1/2 transform -translate-x-1/2 -translate-y-1/2 text-center">
      <span :class="['text-2xl font-bold', overload ? 'text-red-500' : 'text-green-500']" v-if="user">{{ calculateCaloryLeft()  }}</span>
      <div class="flex items-center space-x-4" v-else>
          <USkeleton class="h-4 w-[63px]" />
      </div>
      <span :class="['block text-sm', overload ? 'text-red-500' : 'text-green-500']">Restantes</span>
    </div>
  </div>
</template>

<style scoped>

</style>