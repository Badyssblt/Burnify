<script setup lang="ts">
import {useAuth} from "~/store/auth";

const store = useAuth();

const token = ref(useCookie('token'));

const decoded = computed(() => token.value ? jwtDecode(token.value) : null);

const isAuthenticated = computed(() => store.isAuthenticated);

const items = [
  [{
    label: 'Profil',
    avatar: {
      src: 'https://i.pinimg.com/236x/18/ae/c7/18aec7edee841f7dd0b75bb48f799d81.jpg'
    }
  }], [{
    label: 'Se déconnecter',
    icon: 'i-heroicons-arrow-right-start-on-rectangle',
    click: () => {
      store.logout()
      navigateTo('/login')
    }
  }]
]
</script>

<template>
  <UContainer class="py-6 mb-6 border-b border-white/20 flex justify-between items-center">
    <h1 class="font-bold text-xl">Burnify</h1>
    <div class="flex gap-4" v-if="!isAuthenticated">
      <UButton to="/login">Se connecter</UButton>
      <UButton variant="outline">S'inscrire</UButton>
    </div>
    <UDropdown :items="items" :popper="{ placement: 'bottom-start' }" v-else>
      <UButton variant="ghost" class="dark:hover:bg-transparent"><UAvatar src="https://i.pinimg.com/236x/18/ae/c7/18aec7edee841f7dd0b75bb48f799d81.jpg" size="lg"/></UButton>
    </UDropdown>

  </UContainer>
</template>

<style scoped>

</style>