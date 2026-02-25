<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import { computed } from 'vue';

    const props = defineProps({
        order: {type: Object, required: true},
    });

    const totalAmount = computed(() => {
        return props.order.products.reduce((sum, product) => {
            const taxRate = 1 + product.tax / 100;
            return sum + Math.floor(product.price * product.pivot.quantity * taxRate);
        }, 0);
    });
</script>

<template>
    <Head title="オーダー詳細" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-normal">
                オーダー詳細
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">オーダー詳細</div>
                </div>

                <div class="mt-4 mb-4 ml-4 flex gap-4">
                    <Link
                        :href="route('orders.index')"
                        :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-sm'"
                    >
                        <i class="fa-solid fa-backward"></i> 戻る
                    </Link>
                    <Link
                        :href="route('orders.edit', order.id)"
                        :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-sm'"
                    >
                        <i class="fa-solid fa-edit"></i> 編集
                    </Link>
                    <a
                        :href="route('orders.pdf', order.id)"
                        class="px-4 py-2 bg-red-500 text-white border rounded-md text-sm"
                    >
                        <i class="fa-solid fa-file-pdf"></i> PDF出力
                    </a>
                </div>

                <div class="mt-4 bg-white shadow-sm sm:rounded-lg p-6">
                    <!-- 基本情報 -->
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div>
                            <span class="text-sm text-gray-500">注文ID</span>
                            <p class="text-lg">{{ order.id }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">受注日</span>
                            <p class="text-lg">{{ order.orderday }}</p>
                        </div>
                        <div>
                            <span class="text-sm text-gray-500">顧客名</span>
                            <p class="text-lg">{{ order.customer.name }}</p>
                        </div>
                    </div>

                    <!-- 商品明細 -->
                    <div class="mt-6">
                        <h3 class="text-lg font-semibold mb-2">商品詳細</h3>
                        <table class="table-auto border border-gray-400 w-full">
                            <thead>
                                <tr class="bg-gray-400">
                                    <th class="px-4 py-2 text-xs">商品名</th>
                                    <th class="px-4 py-2 text-xs">価格</th>
                                    <th class="px-4 py-2 text-xs">税率</th>
                                    <th class="px-4 py-2 text-xs">注文数</th>
                                    <th class="px-4 py-2 text-xs">小計（税込）</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="product in order.products" :key="product.id">
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.name }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.price.toLocaleString() }}円</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.tax }}％</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.pivot.quantity }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">
                                        {{ Math.floor(product.price * product.pivot.quantity * (1 + product.tax / 100)).toLocaleString() }}円
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- 合計金額 -->
                    <div class="mt-6 p-4 bg-blue-50 rounded-md border border-blue-200">
                        <div class="text-lg">
                            合計金額（税込）： {{ totalAmount.toLocaleString() }}円
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
