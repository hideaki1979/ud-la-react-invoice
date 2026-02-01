<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';

const props = defineProps({
    products: {type:Object}
});
</script>

<template>
    <Head title="Products" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-normal">
                Products
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">商品一覧</div>
                </div>
            </div>

            <div class="m-3 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                    <div class="mt-4 mb-4 ml-4 flex">
                        <Link
                            :href="route('products.create')"
                            :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-xs'"
                            >
                            <i class="fa-solid fa-plus-circle"></i>
                            商品登録
                        </Link>
                    </div>
                    <table class="table-auto border border-gray-400 w-10/12 m-4">

                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2 w-12">ID</th>
                                <th class="px-4 py-2 w-48">商品</th>
                                <th class="px-4 py-2 w-28">コード</th>
                                <th class="px-4 py-2 w-28 text-center">価格</th>
                                <th class="px-4 py-2 w-28 text-center">税率</th>
                                <th class="px-4 py-2"></th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="product in products" :key="product.id">
                                <td class="border border-gray-400 px-4 py-2">{{ product.id }}</td>
                                <td class="border border-gray-400 px-4 py-2">{{ product.name }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-center">{{ product.code }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-right">{{ product.price }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-right">{{ product.tax }}</td>
                                <td class="border border-gray-400 px-4 py-2 text-center"></td>
                                <td class="border border-gray-400 px-4 py-2 text-center"></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
