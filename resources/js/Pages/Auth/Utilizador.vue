<script setup>
import { reactive } from 'vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({

    name: null,
    email: null,
    password: null,
    password_confirmation: null,
})

const submit = () => {
    // console.log(form)

    form.post("/novoutilizador",{
        onError:()=> form.reset("password"),
    });
}
</script>

<template>

    <Head title="Utilizador" />


    <div class="w-2/4 mx-auto">
        <h1> {{ $page.component }}</h1>

        <form @submit.prevent="submit">
            <div class="mb-6">

                <label>Nome: </label>
                <input type="text" v-model="form.name" />
                <small>{{ form.errors.name }}</small>

            </div>
            <div class="mb-6">

                <label>Email: </label>
                <input type="text" v-model="form.email" />
                <small>{{ form.errors.email }}</small>
            </div>
            <div class="mb-6">
                <label>Password: </label>
                <input type="password" v-model="form.password" />
                <small>{{ form.errors.password }}</small>
            </div>
            <div class="mb-6">
                <label>Confirmar Password: </label>
                <input type="password" v-model="form.password_confirmation" />

            </div>

            <div>
                <p class="text-slate-600 mb-2">Já tens conta ? <a href="#" class="text-link">Login</a></p>

                <button class="primary-btn" :disabled="form.processing">Cadastrar</button>
            </div>
        </form>

    </div>




</template>
