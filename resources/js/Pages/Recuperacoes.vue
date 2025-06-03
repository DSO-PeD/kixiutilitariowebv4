<template>

    <Head title="Recuperações" />
    <div class="container mx-auto px-0 py-6">

        <!-- Formulário de Recuperação -->
        <div class="bg-white rounded-lg shadow-md p-4 mb-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-800">
                    Nova Recuperação
                </h2>
                <button @click="openClientModal" class="btn btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0ZM3 19.235v-.11a6.375 6.375 0 0 1 12.75 0v.109A12.318 12.318 0 0 1 9.374 21c-2.331 0-4.512-.645-6.374-1.766Z" />
                    </svg> &ThinSpace;
                    Selecionar Cliente
                </button>
            </div>

            <form @submit.prevent="guardarRecuperacao" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-receipt mr-1 text-gray-500"></i>
                            Borderoux ID
                        </label>
                        <input v-model="formRecuperacao.txtVoucher" type="text" class="form-input bg-gray-100" readonly
                            required />
                        <span v-if="errors.txtVoucher" class="text-red-500">{{ errors.txtVoucher }}</span>

                        <input v-model="formRecuperacao.textBaseOperacao" type="hidden" />
                        <input v-model="formRecuperacao.textIdComprovativo" type="hidden" />
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-hand-holding-dollar mr-1 text-gray-500"></i>
                            Loan
                        </label>
                        <input v-model="formRecuperacao.Loan" type="text" class="form-input bg-gray-100" readonly
                            required />
                        <span v-if="errors.Loan" class="text-red-500">{{ errors.Loan }}</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-dollar-sign mr-1 text-gray-500"></i>
                            Montante
                        </label>
                        <input v-model="formRecuperacao.txtMontante" type="text" class="form-input bg-yellow-50"
                            readonly required />
                        <span v-if="errors.txtMontante" class="text-red-500">{{ errors.txtVoucher }}</span>
                    </div>

                    <div class="flex flex-col">
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            <i class="fa-solid fa-calendar-check mr-1 text-gray-500"></i>
                            Data Borderoux
                        </label>
                        <input v-model="formRecuperacao.calDataBorderoux" type="text" class="form-input bg-gray-100"
                            readonly required />
                        <span v-if="errors.txtMontante" class="text-red-500">{{ errors.calDataBorderoux }}</span>
                        <input v-model="formRecuperacao.txtBaCodigo" type="hidden" />
                    </div>
                </div>

                <div class="border-l-4 border-orange-400 bg-gray-50 p-4 rounded-lg">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fa-solid fa-user-check mr-1 text-orange-500"></i>
                                Recuperador
                            </label>
                            <select v-model="formRecuperacao.selectRecuperador" class="form-select" required>
                                <option value="" disabled selected>Selecione o recuperador</option>
                                <option v-for="recuperador in $page.props.listar_recuperador" :key="recuperador.id"
                                    :value="recuperador.id">
                                    {{ recuperador.nome_recuperador }}
                                </option>
                            </select>
                        </div>

                        <div class="flex flex-col">
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                <i class="fa-solid fa-calendar-check mr-1 text-orange-500"></i>
                                Data LPF
                            </label>
                            <input v-model="formRecuperacao.txtDataLPF" type="date" class="form-input" required />
                        </div>

                        <div class="flex items-end space-x-2">


                            <button type="submit" class="btn btn-primary2 flex-1 items-center justify-center">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9 3.75H6.912a2.25 2.25 0 0 0-2.15 1.588L2.35 13.177a2.25 2.25 0 0 0-.1.661V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18v-4.162c0-.224-.034-.447-.1-.661L19.24 5.338a2.25 2.25 0 0 0-2.15-1.588H15M2.25 13.5h3.86a2.25 2.25 0 0 1 2.012 1.244l.256.512a2.25 2.25 0 0 0 2.013 1.244h3.218a2.25 2.25 0 0 0 2.013-1.244l.256-.512a2.25 2.25 0 0 1 2.013-1.244h3.859M12 3v8.25m0 0-3-3m3 3 3-3" />
                                </svg>
                                &ThickSpace; Guardar
                            </button>

                            <button type="button" @click="limparCampos" class="btn btn-secondary flex-1">
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                    stroke-width="1.5" stroke="currentColor" class="size-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="M9.53 16.122a3 3 0 0 0-5.78 1.128 2.25 2.25 0 0 1-2.4 2.245 4.5 4.5 0 0 0 8.4-2.245c0-.399-.078-.78-.22-1.128Zm0 0a15.998 15.998 0 0 0 3.388-1.62m-5.043-.025a15.994 15.994 0 0 1 1.622-3.395m3.42 3.42a15.995 15.995 0 0 0 4.764-4.648l3.876-5.814a1.151 1.151 0 0 0-1.597-1.597L14.146 6.32a15.996 15.996 0 0 0-4.649 4.763m3.42 3.42a6.776 6.776 0 0 0-3.42-3.42" />
                                </svg> &ThickSpace;

                                Limpar
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>

        <!-- Alertas -->
        <div v-if="$page.props.flash.success" class="p-4 mb-4 text-sm text-green-800 rounded-lg bg-green-200" role="alert">
            <i class="fa fa-info-circle"></i> {{ $page.props.flash.success }}
        </div>

        <div v-if="$page.props.flash.error" class="p-4 mb-4 text-sm text-green-800 font-meddium rounded-lg bg-green-200" role="alert">
            <i class="fa fa-info-circle"></i> {{ $page.props.flash.error }}
        </div>

        <!-- Tabela de Recuperações -->
        <div class="bg-white rounded-lg shadow-md p-4 md:p-6">
            <!-- Cabeçalho do Card -->
            <div class="flex justify-between mb-6 gap-4">
                <div class="flex space-x-4">
                    <select v-model="estado" @change="filtrarPorEstado($event)" class="form-select pr-8">
                        <option disabled selected :value="''">Escolha estado</option>
                        <option v-for="est in estados" v-bind:key="est.id" v-bind:value="est.id">
                            {{ est.descricao_estado }}</option>
                    </select>

                    <select v-model="agencia" @change="filtrarPorAgencia($event)" class="form-select pr-8">
                        <option disabled selected :value="''">Escolha agência</option>
                        <option v-for="ag in agencias" v-bind:key="ag.OfIdentificador"
                            v-bind:value="ag.OfIdentificador">{{ ag.OfNombre }}</option>
                    </select>
                </div>
                <div class="flex items-center">
                    <a href="/exportar-recuperacao" v-if="auth.user.rec_exporta == 1" preserve-state preserve-scroll
                        class="px-4 text-blue-500 hover:text-blue-600"><i class="fa-solid fa-database"></i> Exportar
                        Script</a>
                    <button class="btn btn-outline-secondary flex items-center gap-2" @click="showModalData = true">
                        <!-- SVG Icon -->
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-5">
                            <path stroke-linecap="round" stroke-linejoin="round"
                                d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5m-9-6h.008v.008H12v-.008ZM12 15h.008v.008H12V15Zm0 2.25h.008v.008H12v-.008ZM9.75 15h.008v.008H9.75V15Zm0 2.25h.008v.008H9.75v-.008ZM7.5 15h.008v.008H7.5V15Zm0 2.25h.008v.008H7.5v-.008Zm6.75-4.5h.008v.008h-.008v-.008Zm0 2.25h.008v.008h-.008V15Zm0 2.25h.008v.008h-.008v-.008Zm2.25-4.5h.008v.008H16.5v-.008Zm0 2.25h.008v.008H16.5V15Z" />
                        </svg>
                        Consultar por Data
                    </button>
                </div>
            </div>
            <hr class="py-2">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
                <div
                    class="bg-gray-100 rounded-xl shadow-lg p-6 border-l-4 border-green-500 hover:shadow-md transition-shadow">
                    <div class="flex justify-between items-start" data-v-644ea457="">
                        <div data-v-644ea457="">
                            <h3 class="text-gray-500 text-sm font-medium uppercase tracking-wider" data-v-644ea457="">Nº
                                Recuperações </h3>
                            <p class="text-3xl font-bold mt-2 text-gray-800" data-v-644ea457="">{{ estatistica["total"]
                                }}</p>
                        </div>
                        <div class="bg-green-100 p-2 rounded-full" data-v-644ea457=""><svg
                                class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"
                                data-v-644ea457="">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" data-v-644ea457=""></path>
                            </svg></div>
                    </div>
                </div>
                <div data-v-644ea457=""
                    class="bg-gray-100 rounded-xl shadow-lg p-6 border-l-4 border-purple-500 hover:shadow-md transition-shadow">
                    <div data-v-644ea457="" class="flex justify-between items-start">
                        <div data-v-644ea457="">
                            <h3 data-v-644ea457="" class="text-gray-500 text-sm font-medium uppercase tracking-wider">
                                Montante(KZ) </h3>
                            <p data-v-644ea457="" class="text-3xl font-bold mt-2 text-gray-800">{{
                                formatMoney(estatistica["somaMontante"]) }}</p>
                        </div>
                        <div data-v-644ea457="" class="bg-purple-100 p-2 rounded-full"><svg data-v-644ea457=""
                                class="w-5 h-5 text-purple-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path data-v-644ea457="" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                                </path>
                            </svg></div>
                    </div>
                </div>
            </div>

            <!--Tabela listagem recuperações-->
            <div class="overflow-x-auto -mx-4 sm:mx-0">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    Registado
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>

                                    Por
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M7.5 3.75H6A2.25 2.25 0 0 0 3.75 6v1.5M16.5 3.75H18A2.25 2.25 0 0 1 20.25 6v1.5m0 9V18A2.25 2.25 0 0 1 18 20.25h-1.5m-9 0H6A2.25 2.25 0 0 1 3.75 18v-1.5M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>

                                    LNR
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0Z" />
                                    </svg>


                                    Cliente
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Zm3 0h.008v.008H18V10.5Zm-12 0h.008v.008H6V10.5Z" />
                                    </svg>

                                    Montante
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M8.25 9.75h4.875a2.625 2.625 0 0 1 0 5.25H12M8.25 9.75 10.5 7.5M8.25 9.75 10.5 12m9-7.243V21.75l-3.75-1.5-3.75 1.5-3.75-1.5-3.75 1.5V4.757c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0c1.1.128 1.907 1.077 1.907 2.185Z" />
                                    </svg>

                                    Bordereau
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg>
                                    Data PGT
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
                                    </svg> Data LPF
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                                    </svg>
                                    Recuperador
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                        stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M3 3v1.5M3 21v-6m0 0 2.77-.693a9 9 0 0 1 6.208.682l.108.054a9 9 0 0 0 6.086.71l3.114-.732a48.524 48.524 0 0 1-.005-10.499l-3.11.732a9 9 0 0 1-6.085-.711l-.108-.054a9 9 0 0 0-6.208-.682L3 4.5M3 15V4.5" />
                                    </svg>

                                    Estado
                                </div>
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Ações</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <tr v-for="rec in lista_recuperacoes.data" :key="rec.id" class="hover:bg-gray-50">
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ formatDate(rec.CiFecha) }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.UtCodigo }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{
                                rec.ReBuDadoOrigem }}</td>
                            <td class="px-4 py-4 text-sm text-gray-500">
                                {{ rec.infoadicional }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm font-semibold text-green-600">{{
                                formatMoney(rec.ReBuMontante) }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.ReBuReferencia }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ rec.ReBuData }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500 text-center">{{ rec.ReBuDataLPF
                                }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-500">{{ rec.nome_recuperador }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                <span :class="rec.color" class="px-2 py-1 text-xs font-medium rounded-full">
                                    {{ rec.estado }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                <a href="#" @click.prevent="confirmarRecuperacao(rec.id)"
                                    v-if="rec.estado == 'Exportado' && auth.user.rec_confirma == 1"
                                    class="text-green-600 hover:bg-green-500 text-green-800 px-2 rounded-full bg-green-300">
                                    <i class="fa-solid fa-check-circle"></i> Confirmar
                                </a>
                            </td>
                        </tr>
                        <tr v-if="lista_recuperacoes.length === 0">
                            <td colspan="12" class="px-4 py-4 text-center text-sm text-gray-500">
                                Nenhum registro encontrado
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <!--Paginação-->
            <div class="mt-4 flex items-center justify-between">
                <p class="text-md  text-blue-500 font-medium">
                    <span class="font-medium">Página: </span> {{ lista_recuperacoes.current_page }} de {{
                        lista_recuperacoes.last_page }}
                </p>
                <div class="mt-4 space-x-2">
                    <button v-if="lista_recuperacoes.prev_page_url"
                        @click="$inertia.visit(lista_recuperacoes.prev_page_url)"
                        class="px-2 py-1 bg-blue-500 rounded text-white">
                        << Anterior </button>

                            <button v-if="lista_recuperacoes.next_page_url"
                                @click="$inertia.visit(lista_recuperacoes.next_page_url)"
                                class="px-2 py-1 bg-blue-500 rounded text-white">
                                Seguinte >>
                            </button>
                </div>
            </div>
        </div>

        <!-- ... (modais mantidos iguais) ... -->
        <ModalDate :isOpen="showModalData" @close="showModalData = false" @search="buscarPorDatas"
            v-model:dataInicio="dataInicio" v-model:dataFim="dataFim" v-model:estadoModal="estadoModal" v-model:agenciaModal="agenciaModal" />
        <!-- Modal de Seleção de Cliente -->
        <Modal :show="showClientModal" @close="closeClientModal" maxWidth="3xl">
            <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-4 rounded-t-lg">
                <div class="flex justify-between items-center">
                    <h3 class="text-lg font-bold">
                        <i class="fa-solid fa-users-viewfinder mr-2"></i>
                        Selecionar Cliente
                    </h3>
                    <button @click="closeClientModal" class="text-white hover:text-gray-200">
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                            stroke="currentColor" class="size-6">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>

                    </button>
                </div>
            </div>
            
            <div class="bg-gray-800 p-4">
                <div class="overflow-x-auto">
                    <div class="py-4">
                        <input v-model="nome_cliente" type="text" placeholder="pesquisar cliente" class="flex justify-end bg-white border border-gray-300 text-gray-900 text-sm rounded-2xl focus:ring-blue-500 focus:border-blue-500 px-3 py-1">
                    </div>
                    <table class="min-w-full divide-y divide-gray-700">
                        <thead class="bg-gray-700">
                            <tr>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Loan</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Voucher</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Cliente</th>
                                <th
                                    class="px-4 py-3 text-left text-xs font-medium text-orange-400 uppercase tracking-wider">
                                    Ação</th>
                            </tr>
                        </thead>
                        <tbody class="bg-gray-800 divide-y divide-gray-700">
                            <tr v-for="voucher in clientesFiltrados"
                                :key="voucher.idComprovativo" class="hover:bg-gray-700">
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ voucher.BuDadoOrigem }}
                                </td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-gray-300">{{ voucher.BuReferencia }}
                                </td>
                                <td class="px-4 py-4 text-sm text-gray-300">{{ voucher.infoadicional }}</td>
                                <td class="px-4 py-4 whitespace-nowrap text-sm text-center">
                                    <button @click="selectClient(voucher)" class="btn btn-warning btn-sm">
                                        <i class="fa-solid fa-file-import mr-1"></i>
                                        Selecionar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="bg-gray-100 px-4 py-3 flex justify-end">
                <button @click="closeClientModal" class="btn btn-secondary">
                    <i class="fa-solid fa-times mr-2"></i>
                    Fechar
                </button>
            </div>
        </Modal>
    </div>
</template>

<script setup>
import { onMounted, reactive, ref, computed } from 'vue'
import { Inertia } from '@inertiajs/inertia'
import { router, usePage } from '@inertiajs/vue3'
import Modal from '@/Components/Modal.vue';
import ModalDate from './Layouts/components/ComprovativosComponents/ModalDate.vue'
import Pagination from '@/Components/Pagination.vue'; // Componente de paginação

// Props recebidas do backend
defineProps({
    lista_recuperacoes: Object, // Agora é um objeto com paginação
    listar_recuperador: Array,
    listar_estados: Array,
    BasesOperacao: Array,
    lista_agencias_consultas: Array,
    listar_voucher_para_recuperacao: Array,
    estatistica: Array,
    auth: Object
});

const showModalData = ref(false)

// Estados de controle
const errors = ref({})
const loading = ref(false)
const successMessage = ref('')

const estados = ref([]);
const listarEstados = async () => {
    const res = await fetch('/listarEstados');
    const json = await res.json();
    estados.value = json;
}

const agencias = ref([]);
const listarAgencias = async () => {
    const res = await fetch('/listarAgencias');
    const json = await res.json();
    agencias.value = json;
}

//recebe de volta os parametros escolhidos na select para ficar estático mesmo que demos next na paginação
const props = usePage().props
let estado = ref(props.filtros.estado || '');
let agencia = ref(props.filtros.agencia || '');

let totalRecup = ref(props.estatistica["total"] || 0);
let montanteRecup = ref(0);

const filtrarPorEstado = async (event) => {
    agencia.value = '';
}

const filtrarPorAgencia = async (event) => {
    router.get('/recuperacoes',
        {
            estado: estado.value,
            agencia: agencia.value,
        }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Função para buscar por datas
const buscarPorDatas = async (dates) => {
    try {
        agencia.value = '';
        estado.value = '';
        totalRecup.value = 0;
        montanteRecup.value = 0;
        router.get('/recuperacoes', 
            { 
                tipo: 3, 
                dataIn: dataInicio.value, 
                dataF: dataFim.value,
                estado: estadoModal.value,
                agencia: agenciaModal.value
                
            }, {
            preserveState: true,
            preserveScroll: true,
        });
        showModalData.value = false;
    } catch (error) {
        console.error('Erro ao buscar por datas:', error);
    }
};

const confirmarRecuperacao = (idRecuperacao) => {
    router.get('/confirmarRecuperacao',
        {
            id: idRecuperacao
        }, {
        preserveState: true,
        preserveScroll: true,
    });
}

// Estado do formulário
const formRecuperacao = reactive({
    txtVoucher: '',
    textBaseOperacao: '',
    textIdComprovativo: '',
    Loan: '',
    txtMontante: '',
    calDataBorderoux: '',
    txtBaCodigo: '',
    selectRecuperador: '',
    txtDataLPF: '',
});

//Filtrar cliente numa tabela de clientes para recuperação
const nome_cliente = ref('');
const clientesFiltrados = computed(() => {
  return props.listar_voucher_para_recuperacao.filter(voucher =>
    voucher.infoadicional.toLowerCase().includes(nome_cliente.value.toLowerCase())
  );
});

// Função para submeter
function guardarRecuperacao() {
    loading.value = true
    successMessage.value = ''
    errors.value = {}

    Inertia.post('/guardar-recuperacao', formRecuperacao, {
        onSuccess: () => {
            successMessage.value = 'Dados guardados com sucesso!'
            loadRecuperacoes(); // Recarregar a lista após inserção
        },
        onError: (err) => {
            errors.value = err
        },
        onFinish: () => {
            loading.value = false
        }
    })
}

//Estados do componente
const showDeleteModal = ref(false);
const selectedRecuperacao = ref(null);
const showClientModal = ref(false);

// Funções auxiliares
const formatDate = (date) => {
    if (!date) return '';
    const d = new Date(date);
    return d.toLocaleDateString('pt-PT') + ' ' + d.toLocaleTimeString('pt-PT').slice(0, 5);
};

const formatMoney = (amount) => {
    return parseFloat(amount).toLocaleString('pt-PT', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    });
};

// Funções de manipulação de modais
const openClientModal = () => showClientModal.value = true;
const closeClientModal = () => showClientModal.value = false;
const dataInicio = ref('')
const dataFim = ref('')
const estadoModal = ref('')
const agenciaModal = ref('')

const confirmDelete = () => {
    if (!selectedRecuperacao.value) return;

    Inertia.delete(`/recuperacoes/${selectedRecuperacao.value.id}`, {
        onSuccess: () => {
            loadRecuperacoes(); // Recarregar a lista após exclusão
            showDeleteModal.value = false;
        },
        onError: () => {
            alert('Erro ao excluir recuperação');
        }
    });
};

// Função para selecionar um cliente
const selectClient = (client) => {
    try {
        console.log('Selecionando cliente:', client);
        formRecuperacao.txtVoucher = client.BuReferencia;
        formRecuperacao.textBaseOperacao = client.BaseOperacao || '';
        formRecuperacao.textIdComprovativo = client.idComprovativo;
        formRecuperacao.Loan = client.BuDadoOrigem;
        formRecuperacao.txtMontante = client.BuMontante || '';
        formRecuperacao.calDataBorderoux = client.BuData || '';
        formRecuperacao.txtBaCodigo = client.BaCodigo || '';
        closeClientModal();
    } catch (error) {
        console.error('Erro ao selecionar cliente:', error);
    }
};

const limparCampos = () => {
    formRecuperacao.txtVoucher = '';
    formRecuperacao.textBaseOperacao = '';
    formRecuperacao.textIdComprovativo = '';
    formRecuperacao.Loan = '';
    formRecuperacao.txtMontante = '';
    formRecuperacao.calDataBorderoux = '';
    formRecuperacao.txtBaCodigo = '';
    formRecuperacao.selectRecuperador = '';
    formRecuperacao.txtDataLPF = '';
};

onMounted(async () => {
    listarEstados();
    listarAgencias();
});
</script>

<style scoped>
/* ... (estilos mantidos iguais) ... */

/* Estilos adicionais para ordenação */
.sortable-header {
    @apply cursor-pointer hover:bg-gray-100 transition-colors;
}

.sort-icon {
    @apply ml-1;
}

.form-input,
.form-select {
    @apply border border-gray-300 rounded-md px-3 py-2 w-full focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors;
}

.btn {
    @apply px-4 py-2 rounded-md font-medium transition-colors flex items-center justify-center;
}

.btn-primary {
    @apply bg-gradient-to-r from-green-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
}

.btn-primary2 {
    @apply bg-gradient-to-r from-orange-900 to-greenkixi-300 text-white hover:from-green-800 shadow-md hover:shadow-lg;
}

.btn-outline {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-secondary {
    @apply border border-gray-300 bg-white text-gray-700 hover:bg-gray-50;
}

.btn-outline-excel {
    @apply border border-green-900 bg-white text-green-900 hover:bg-green-50;
}



.btn-secondary {
    @apply bg-gray-200 text-gray-800 hover:bg-gray-300;
}

.btn-danger {
    @apply bg-red-600 text-white hover:bg-red-700;
}

.btn-warning {
    @apply bg-yellow-500 text-white hover:bg-yellow-600;
}

.btn-sm {
    @apply px-3 py-1 text-sm;
}

.bg-greenkixi-300 {
    background-color: #08583d;
}

.text-greenkixi-solid {
    color: #08583d;
}

.to-greenkixi-300 {
    --tw-gradient-to: #08583d;
}

/* Animações para o modal */
.modal-enter-active {
    animation: modal-fade 0.3s ease-out;
}

@keyframes modal-fade {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
