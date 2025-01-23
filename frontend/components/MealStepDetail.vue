<script setup lang="ts">
const isOpenDetails = defineModel();

const props = defineProps({
  mealStep: Object,
  step: String,
  meal: Object
})

const { $api } = useNuxtApp();

const deleteFood = async (id) => {
  try {
    await $api.delete(`/api/food/${id}`)
    getMeals()
  }catch (e) {

  }
}
const getMeals = inject("getMeals")

</script>

<template>
  <UModal v-model="isOpenDetails" v-if="meal">
    <div class="p-4 h-[calc(100vh-50px)] overflow-scroll">
      <div class="border-b border-white/20 pb-4">
        <div class="flex gap-4">
          <UButton @click="isOpenDetails = false" class="p-0" variant="ghost"><UIcon class="w-6 h-6 text-white" name="i-heroicons-arrow-long-left"/></UButton>
          <h5 class="text-lg font-bold">{{ mealStep[step].title }}</h5>
        </div>
      </div>

      <div class="flex flex-col gap-3">
        <div v-if="meal?.food.length === 0">
          <p class="mt-4">Aucun aliment n'a été ajouté.</p>
        </div>
        <div v-for="food in meal.food" v-if="meal" class="flex justify-between items-center border-b border-white/20 pb-3 pt-3">
          <div>
            <p>{{ food.name }}</p>
            <span class="text-white/60 text-sm">{{ food.weight }}  {{ food.unit }}</span>
          </div>
          <div class="flex items-center">
            <p>{{ food.calories }} kcal</p>
            <UButton variant="ghost" icon="i-heroicons-trash" @click="deleteFood(food.id)"></UButton>
          </div>
        </div>
      </div>
    </div>
  </UModal>
</template>

<style scoped>

</style>