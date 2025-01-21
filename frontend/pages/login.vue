<script setup lang="ts">

import {useAuth} from "~/store/auth";

interface LoginForm {
  email: string;
  password: string;
}

const form = ref<LoginForm>({
  email: '',
  password: ''
})


const loadingState = ref<boolean>(false);
const error = ref<boolean>(false);
const errorMessage = ref<string>('');

const { $api } = useNuxtApp();
const store = useAuth();

const login = async () => {
  loadingState.value = true;
  error.value = false;
  errorMessage.value = "";
  console.log(form)
  try {
    const response = await $api.post('/api/login_check', {
      username: form.value.email,
      password: form.value.password
    });
    useCookie("token").value = response.data.token;
    store.token = response.data.token;
    navigateTo("/")
  }catch (e) {
    console.log(e)
    loadingState.value = true;
    if(e.status === 401){
      error.value = true;
      errorMessage.value = "Email ou mot de passe incorrect..."
    }
  }
  loadingState.value = false;
}
</script>

<template>
  <UContainer>
    <UForm size="sm" :state="form" @submit="login">
      <UFormGroup label="Email" name="email" size="lg">
        <UInput placeholder="johndoe@gmail.com" icon="i-heroicons-envelope" v-model="form.email"/>
      </UFormGroup>
      <UFormGroup label="Mot de passe" name="password" size="lg" class="mt-4">
        <UInput placeholder="********" v-model="form.password"/>
      </UFormGroup>

      <UButton type="submit" class="mt-6" block :loading="loadingState">Se connecter</UButton>
    </UForm>
  </UContainer>
</template>

<style scoped>

</style>