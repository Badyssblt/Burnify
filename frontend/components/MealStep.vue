<script setup lang="ts">
import axios from "axios";

const props = defineProps({
  step: {
    type: String,
    required: true
  },
  meal: {
    type: Object,
    required: false
  },
  currentDate: {
    type: [String, Date],
    required: false
  }
})

const getMeals = inject('getMeals');
const barcode = ref<string>("");


/**
 * Permet de ne pas activer le composant barcode si la ref barcode
 * est null (le cas ou l'utilisateur revient en arrière après avoir scanner)
 */
const scanNotActive = ref(false);

const isScannerOpen = ref<boolean>(false);

const mealStep = ref({
  breakfast: {
    title: 'Petit-déjeuner',
    icon: 'i-cib:buy-me-a-coffee'
  },
  lunch: {
    title: 'Déjeuner',
    icon: 'i-material-symbols:fork-spoon'
  },
  dinner: {
    title: 'Diner',
    icon: 'i-material-symbols:soup-kitchen'
  }
});

const customFoodForm = reactive({
  quantity: undefined
})



const { $api } = useNuxtApp();

const foods = ref<array>([]);
const query = ref<string>('');


const getFoods = async () => {
  loadingState.value = true;
  try {
    const limit = 10;
    const url = `https://world.openfoodfacts.net/api/v2/search?categories_tags_fr=Chocolat&fields=product_name_fr,product_name,quantity,energy-kcal_100g,code&locale=fr&page_size=${limit}`;

    const response = await $api.get(url);

    foods.value = response.data.products;

  }catch (e) {
    console.log(e)
  }
  loadingState.value = false;
}

const search = async () => {
  foods.value = []
  loadingState.value = true;
  console.log(loadingState);

  try {
    const url = `https://world.openfoodfacts.org/cgi/search.pl?search_terms=${query.value}&search_simple=1&action=process&json=1&fields=product_name,code,product_name_fr,product_quantity_unit,code,energy-kcal_100g`;

    const response = await axios.get(url);

    const uniqueProducts = [];
    const productNames = new Set();

    response.data.products.forEach(product => {
      const nameToCheck = product.product_name || product.product_name_fr;
      if (!productNames.has(nameToCheck)) {
        uniqueProducts.push(product);
        productNames.add(nameToCheck);
      }
    });

    foods.value = uniqueProducts;

  } catch (e) {
    console.log(e);
  }

  loadingState.value = false;
}


const isOpen = ref<boolean>(false)
const isOpenDetails = ref<boolean>(false)

const createMeal = async () => {
  try {
    const response = await $api.post('/api/meals', {
      type: props.step,
      calories: 0,
      created_at: props.currentDate
    });
    return response.data;
  }catch (e){
    console.log(e)
  }
}

const addFood = async (food) => {
    if(!props.meal) {
      await createMeal()
      await getMeals();
    }
    try {
      const response = await $api.post('/api/food', {
        identifier: `${food.code}`,
        calories: food['energy-kcal_100g'],
        meal: `/api/meals/${props.meal.id}`
      })
      await getMeals();
    }catch (e) {
      console.log(e)
    }
}

const currentFood = ref();

const fetchFood = async () => {
  currentFood.value = null;
  try {
    const response = await axios.get(`https://world.openfoodfacts.org/api/v0/product/${barcode.value}.json`)

    currentFood.value = response.data
  }catch (e) {
    console.log(e)
  }
}



const loadingState = ref<boolean>(true);


</script>

<template>
  <div class="border-b border-white/20 pb-4 flex justify-between items-center" >
    <div class="flex items-center gap-4" @click="isOpenDetails = true">
      <div class="relative size-10">
        <svg class="size-full -rotate-90" viewBox="0 0 36 36" xmlns="http://www.w3.org/2000/svg">
          <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-gray-200 dark:text-neutral-700" stroke-width="2"></circle>
          <circle cx="18" cy="18" r="16" fill="none" class="stroke-current text-green-500" stroke-width="2" stroke-dasharray="100" stroke-dashoffset="0" stroke-linecap="round"></circle>
        </svg>

        <div class="absolute top-1/2 start-1/2 transform -translate-y-1/2 -translate-x-1/2 ">
          <span class="text-center font-bold text-green-500 flex items-center"><UIcon :name="mealStep[step].icon"/></span>
        </div>
      </div>

      <div>
        <h4>{{ mealStep[step].title }}</h4>
      </div>
    </div>

    <div>
      <UButton @click="isOpen = true; getFoods()" :ui="{ rounded: 'rounded-full' }" class="w-6 h-6 p-0 flex justify-center items-center"><UIcon name="i-heroicons-plus" class="w-4 h-4"/></UButton>
    </div>
    <UModal v-model="isOpenDetails">
      <div class="p-4 h-[calc(100vh-50px)] overflow-scroll">
        <div class="border-b border-white/20 pb-4">
          <div class="flex gap-4">
            <UButton @click="isOpenDetails = false" class="p-0" variant="ghost"><UIcon class="w-6 h-6 text-white" name="i-heroicons-arrow-long-left"/></UButton>
              <h5 class="text-lg font-bold">{{ mealStep[step].title }}</h5>
          </div>
        </div>
      </div>
    </UModal>
    <UModal v-model="isOpen">
      <div class="p-4 h-[calc(100vh-50px)] overflow-scroll">
        <div class="border-b border-white/20 pb-4">
          <div class="flex justify-between">
            <div>
              <div class="flex gap-4 items-center">
                <UButton @click="isOpen = false" class="p-0" variant="ghost"><UIcon class="w-6 h-6 text-white" name="i-heroicons-arrow-long-left"/></UButton>
                <h5 class="text-lg font-bold">{{ mealStep[step].title }}</h5>
              </div>
              <p class="text-sm text-white/60" v-if="meal">{{ meal?.calories }} kcal</p>
            </div>
            <p>{{ meal?.food.length || 0}}</p>
            <UButton @click="isScannerOpen = true; scanNotActive = true">Scan</UButton>

            <UModal v-model="isScannerOpen">
              <Barcode v-if="!barcode && scanNotActive" @scanSuccess="fetchFood" v-model="barcode"/>
              <div class="p-4 h-[calc(100vh-50px)] overflow-hidden" v-if="barcode && currentFood" :key="currentFood.product.product_name_fr">
                <div class="p-4 h-[calc(80vh-50px)] overflow-scroll relative">
                  <div class="border-b border-white/20 pb-4">
                    <div class="flex gap-4">
                      <UButton @click="isScannerOpen = false; barcode = null; scanNotActive = false" class="p-0" variant="ghost"><UIcon class="w-6 h-6 text-white" name="i-heroicons-arrow-long-left"/></UButton>
                      <h5 class="text-lg font-bold">{{ mealStep[step].title }}</h5>
                    </div>
                  </div>
                  <p class="font-medium mt-4 text-lg">{{ currentFood.product.product_name_fr || currentFood.product.product_name }}</p>
                  <div class="flex justify-between mt-6">
                    <div class="flex flex-col items-center">
                      <p class="font-bold">
                        {{ currentFood.product && currentFood.product.nutriments && currentFood.product.nutriments['energy-kcal_100g'] || currentFood.product && currentFood.product.nutriments_estimated && currentFood.product.nutriments_estimated['energy-kcal_100g'] || 'Non renseigné' }}
                      </p>
                      <span class="text-sm">Calories</span>
                    </div>

                    <div class="flex flex-col items-center">
                      <p class="font-bold">
                        {{ currentFood.product && currentFood.product.nutriments && currentFood.product.nutriments['carbohydrates_100g'] || currentFood.product && currentFood.product.nutriments_estimated && currentFood.product.nutriments_estimated['carbohydrates_100g'] || 'Non renseigné' }}
                      </p>
                      <span class="text-sm">Glucides</span>
                    </div>

                    <div class="flex flex-col items-center">
                      <p class="font-bold">
                        {{ currentFood.product && currentFood.product.nutriments && currentFood.product.nutriments['proteins_100g'] || currentFood.product && currentFood.product.nutriments_estimated && currentFood.product.nutriments_estimated['proteins_100g'] || 'Non renseigné' }}
                      </p>
                      <span class="text-sm">Protéines</span>
                    </div>

                    <div class="flex flex-col items-center">
                      <p class="font-bold">
                        {{ currentFood.product && currentFood.product.nutriments && currentFood.product.nutriments['fat_100g'] || currentFood.product && currentFood.product.nutriments_estimated && currentFood.product.nutriments_estimated['fat_100g'] || 'Non renseigné' }}
                      </p>
                      <span class="text-sm">Lipides</span>
                    </div>
                  </div>




                  <div class="fixed bottom-2 w-full left-0 px-6 py-4">
                    <div class="flex-1 md:flex-none">
                      <UForm :state="customFoodForm">
                        <UFormGroup>
                          <UInput name="quantity">
                            <template #trailing>
                              <span class="text-gray-500 dark:text-gray-400 text-xs">{{ currentFood.product.product_quantity_unit }}</span>
                            </template>
                          </UInput>
                          <UButton icon="i-heroicons-plus" class="mt-4" block>Ajouter</UButton>
                        </UFormGroup>
                      </UForm>
                    </div>
                  </div>
                </div>

              </div>
            </UModal>
          </div>
          <UForm class="mt-2" @submit="search" :state="{}">
            <UFormGroup :eager-validation="true">
              <UInput
                  icon="i-heroicons-magnifying-glass-20-solid"
                  size="sm"
                  color="white"
                  :trailing="false"
                  placeholder="Rechercher..."
                  v-model="query"
              />
            </UFormGroup>

          </UForm>
        </div>
        <div class="mt-4 flex flex-col gap-6" v-if="loadingState">
          <div class="flex items-center space-x-4" v-for="n in 10">
            <div class="space-y-2">
              <USkeleton class="h-4 w-[250px]" />
              <USkeleton class="h-4 w-[200px]" />
            </div>
          </div>
        </div>
        <div class="mt-4 flex flex-col gap-4" v-else>
          <div class="flex items-center justify-between space-x-4" v-for="food in foods">
            <div>
              <p>{{ food.product_name_fr || food.product_name }}</p>
              <p class="text-sm text-white/50">100 {{ food['product_quantity_unit'] == 'ml' ? 'ml' : 'g' }}</p>
            </div>
            <div class="flex items-center gap-2">
              <p class="font-bold text-green-500">{{ food['energy-kcal_100g'] }} kcal</p>
              <UButton @click="addFood(food)" :ui="{ rounded: 'rounded-full' }" class="w-6 h-6 p-0 flex justify-center items-center"><UIcon name="i-heroicons-plus"/></UButton>
            </div>
          </div>
        </div>
      </div>
    </UModal>
  </div>
</template>

<style scoped>

</style>