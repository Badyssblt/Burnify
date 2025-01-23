<script setup lang="ts">
import Summary from "~/components/Summary.vue";
import { isToday, isYesterday, format } from 'date-fns';
import { fr } from 'date-fns/locale/index.js';

useHead({
  title: 'Accueil'
});

const date = ref(new Date());

const dayText = computed(() => {
  const inputDate = new Date(date.value);

  if (isToday(inputDate)) {
    return "Aujourd'hui";
  }

  if (isYesterday(inputDate)) {
    return "Hier";
  }

  return format(inputDate, 'dd MMMM', { locale: fr });

});


const { $api } = useNuxtApp();

const meals = ref<array>([]);

const isLoading = ref<boolean>(false);

const user = ref();

const overload = ref(false);


const getMeals = async () => {
  isLoading.value = true;
  try {
    const formattedDate = format(date.value, 'MM-dd-yyyy');
    const response = await $api.get('/api/meals?date=' + formattedDate);

    meals.value = response.data;

    isOverload()
  }catch (e) {
    console.log(e)
  }
  isLoading.value = false;
}

const isOverload = () => {
  const totalCaloriesConsommed = meals.value[1]

  if(user.value?.caloryPerDay - totalCaloriesConsommed < 0){
    overload.value = true
  }else {
    overload.value = false;
  }
}

const getUserInfo = async () => {
  try {
    const response = await $api.get('/api/user/me')
    user.value = response.data
    isOverload()
  }catch (e) {

  }
}


provide('getMeals', getMeals)

onMounted(async () => {
  await getMeals()
  await getUserInfo()
})

</script>

<template>
  <UContainer>
    <div class="flex justify-between items-center">
      <h2 class="font-medium text-lg">{{ dayText }}</h2>
      <UPopover :popper="{ placement: 'bottom-start' }">
        <UButton color="white" variant="link"><UIcon name="i-heroicons-calendar" class="w-6 h-6"/></UButton>
        <template #panel="{ close }">
          <DatePicker v-model="date" is-required @close="getMeals" @change="getMeals()"/>
        </template>
      </UPopover>
    </div>

    <div class="my-6">
      <h3>Résumé</h3>
      <UCard class="mt-4">
        <div class="flex justify-between items-center">
          <div>
            <p class="flex flex-col items-center"><span class="font-bold">{{ meals[1] ?? '0' }}</span> <span class="text-sm text-gray-400">Consommées</span></p>
          </div>
          <Summary v-if="user" :meals="meals" :user="user" :overload="overload"/>
          <div>
            <p class="flex flex-col items-center"><span class="font-bold">0</span> <span class="text-sm text-gray-400">Restantes</span></p>
          </div>
        </div>
      </UCard>
    </div>

    <div>
      <h3>Alimentation</h3>
      <UCard class="mt-4">
        <div class="flex flex-col gap-4" v-if="!isLoading">
          <MealStep step="breakfast" :meal="meals[0]?.breakfast" :currentDate="date"/>
          <MealStep step="lunch" :meal="meals[0]?.lunch" :currentDate="date"/>
          <MealStep step="dinner" :meal="meals[0]?.dinner" :currentDate="date"/>
        </div>
        <div v-else class="flex flex-col gap-4">
          <div class="flex items-center space-x-4">
            <USkeleton class="h-12 w-12" :ui="{ rounded: 'rounded-full' }" />
            <div class="space-y-2">
              <USkeleton class="h-4 w-[250px]" />
              <USkeleton class="h-4 w-[200px]" />
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <USkeleton class="h-12 w-12" :ui="{ rounded: 'rounded-full' }" />
            <div class="space-y-2">
              <USkeleton class="h-4 w-[250px]" />
              <USkeleton class="h-4 w-[200px]" />
            </div>
          </div>
          <div class="flex items-center space-x-4">
            <USkeleton class="h-12 w-12" :ui="{ rounded: 'rounded-full' }" />
            <div class="space-y-2">
              <USkeleton class="h-4 w-[250px]" />
              <USkeleton class="h-4 w-[200px]" />
            </div>
          </div>
        </div>
      </UCard>
    </div>
  </UContainer>

  <BottomBar/>
</template>

<style scoped>

</style>