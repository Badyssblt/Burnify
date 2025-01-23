<script setup>

const model = defineModel()

const props = defineProps({
  currentFood: Object,
  step: String,
  mealStep: Object,
  meal: Object
})

const getNutrientValue = (currentFood, nutrientKey) =>  {
  if (props.currentFood.product?.nutriscore?.['2021']?.data?.is_water === "1") {
    return 0;
  }
  if (
      props.currentFood &&
      props.currentFood.nutriments &&
      props.currentFood.nutriments[nutrientKey]
  ) {
    return props.currentFood.nutriments[nutrientKey];
  } else if (
      props.currentFood &&
      props.currentFood.nutriments_estimated &&
      props.currentFood.nutriments_estimated[nutrientKey]
  ) {
    return props.currentFood.nutriments_estimated[nutrientKey];
  } else {
    return 'Non renseigné';
  }
}

const addFoodForm = ref({
  quantity: 100
})

const getMeals = inject("getMeals")
const createMeal = inject("createMeal")

const { $api } = useNuxtApp()

const addFood = async (food) => {
  if(!props.meal) {
    await createMeal()
    await getMeals();
  }
  try {
    let calories = props.currentFood.nutriments['energy-kcal_100g'];
    let caloriesParGramme = props.currentFood.nutriments['energy-kcal_100g'] / 100;
    if (addFoodForm.value.quantity !== undefined && addFoodForm.value.quantity > 0) {
      calories = Math.round(caloriesParGramme * addFoodForm.value.quantity);
    }
    const response = await $api.post('/api/food', {
      identifier: `${food.code}`,
      calories,
      name: props.currentFood.product_name_fr || props.currentFood.product_name,
      meal: `/api/meals/${props.meal.id}`,
      unit: props.currentFood.product_quantity_unit || 'g',
      weight: addFoodForm.value.quantity
    })
    await getMeals();
  }catch (e) {
    console.log(e)
  }
}

</script>

<template>
  <UModal v-model="model">
    <div class="p-4 h-[calc(100vh-50px)] overflow-hidden" v-if="currentFood">
      <div class="p-4 h-[calc(80vh-50px)] overflow-scroll relative">
        <div class="border-b border-white/20 pb-4">
          <div class="flex gap-4">
            <UButton @click="model = false" class="p-0" variant="ghost"><UIcon class="w-6 h-6 text-white" name="i-heroicons-arrow-long-left"/></UButton>
            <h5 class="text-lg font-bold">{{ mealStep[step].title }}</h5>
          </div>
        </div>
        <p class="font-medium mt-4 text-lg">{{ currentFood.product_name_fr || currentFood.product_name }}</p>
        <div class="flex justify-between mt-6">
          <div class="flex flex-col items-center">
            <p class="font-bold">
              {{ getNutrientValue(currentFood, 'energy-kcal_100g') }}
            </p>
            <span class="text-sm">Calories</span>
          </div>

          <div class="flex flex-col items-center">
            <p class="font-bold">
              {{ getNutrientValue(currentFood, 'carbohydrates_100g') }}
            </p>
            <span class="text-sm">Glucides</span>
          </div>

          <div class="flex flex-col items-center">
            <p class="font-bold">
              {{ getNutrientValue(currentFood, 'proteins_100g') }}
            </p>
            <span class="text-sm">Protéines</span>
          </div>

          <div class="flex flex-col items-center">
            <p class="font-bold">
              {{ getNutrientValue(currentFood, 'fat_100g') }}
            </p>
            <span class="text-sm">Lipides</span>
          </div>
        </div>




        <div class="fixed bottom-2 w-full left-0 px-6 py-4">
          <div class="flex-1 md:flex-none" v-if="currentFood">
            <UForm :state="addFoodForm" @submit="addFood({
                        code: `${currentFood.code}`,
                        'energy-kcal_100g': getNutrientValue(currentFood, 'energy-kcal_100g'),
                        meal: `/api/meals/${props.meal?.id}`
                      })">
              <UFormGroup>
                <UInput name="quantity" type="number" v-model="addFoodForm.quantity">
                  <template #trailing>
                    <span class="text-gray-500 dark:text-gray-400 text-xs">{{ currentFood.product_quantity_unit || 'g' }}</span>
                  </template>
                </UInput>
                <UButton icon="i-heroicons-plus" class="mt-4" block type="submit">Ajouter</UButton>
              </UFormGroup>
            </UForm>
          </div>
        </div>
      </div>
    </div>
  </UModal>


</template>

<style scoped>

</style>