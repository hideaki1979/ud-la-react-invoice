<script setup>
import DangerButton from '@/Components/DangerButton.vue';
import TextInput from '@/Components/TextInput.vue';
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    orders: {type: [Object, Array]},
    search_str: String,
    flash: Object,
});

const flash = computed(() => props.flash);

const form = useForm({
    id: '',
    search_str: props.search_str || '',
});

const deleteOrder = (id, customerName) => {
    if (confirm(customerName + "を削除して良いですか？")) {
        form.delete(route('orders.destroy', id), {
            onSuccess: () => {

            },
            onError: (errors) => {
                console.error('削除に失敗しました。', errors);
                alert('注文の削除に失敗しました。')
            }
        });
    }
};

const search_go = () => {
    form.get(route('orders.index'));
};

const links = computed(() => {
    return Array.isArray(props.orders) ? [] : (props.orders.links ?? [] );
});

</script>

<template>
    <Head title="オーダー情報" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="text-xl text-gray-800 leading-normal">
                オーダー情報
            </h2>
        </template>

        <div class="py-8">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-4 text-gray-900">オーダー一覧</div>
                </div>
            </div>

            <div class="m-4 max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-2">
                    <div class="mt-4 mb-4 ml-4 flex gap-4">
                        <Link
                            :href="route('orders.create')"
                            :class="'px-4 py-2 bg-indigo-500 text-white border rounded-md text-xs'"
                        >
                            <i class="fa-solid fa-plus-circle"></i> オーダー登録
                        </Link>
                        <div>
                            <TextInput
                                id="search_str"
                                type="text"
                                class="block w-full"
                                v-model="form.search_str"
                                autocomplete="search_str"
                                @blur="search_go"
                            />
                        </div>
                        <span v-if="props.orders.data.length===0" class="m-2">
                            該当する注文はありません
                        </span>
                    </div>

                    <div v-if="flash?.success" class="bg-green-100 p-4 m-4 w-3/4">
                        {{ flash.success }}
                    </div>

                    <table class="table-auto border border-gray-400 w-100 m-4">
                        <thead>
                            <tr class="bg-gray-100">
                                <th class="px-4 py-2">ID</th>
                                <th class="px-4 py-2 text-xs">受注日</th>
                                <th class="px-4 py-2 text-xs">顧客</th>
                                <th class="px-4 py-2 text-xs">商品</th>
                                <th class="px-4 py-2 text-xs">価格</th>
                                <th class="px-4 py-2 text-xs">税率</th>
                                <th class="px-4 py-2 text-xs">注文数</th>
                                <th class="px-4 py-2"></th>
                                <th class="px-4 py-2"></th>
                                <th class="px-4 py-2"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template v-for="order in orders.data" :key="order.id">
                                <tr v-for="(product, idx) in order.products" :key="product.id">
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">{{ order.id }}</td>
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">{{ order.orderday }}</td>
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">{{ order.customer.name }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.name }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.price }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.tax }}</td>
                                    <td class="border border-gray-400 px-4 py-2 text-center">{{ product.pivot.quantity }}</td>
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">
                                        <Link :href="route('orders.show', order.id)"
                                            :class="'px-4 py-2 bg-blue-500 text-white border rounded-md text-xs'"
                                            aria-label="注文詳細を表示"
                                        >
                                            <i class="fa-solid fa-eye"></i>
                                        </Link>
                                    </td>
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">
                                        <Link :href="route('orders.edit', order.id)"
                                            :class="'px-4 py-2 bg-yellow-500 text-white border rounded-md text-xs'"
                                            aria-label="注文を編集"
                                        >
                                            <i class="fa-solid fa-edit"></i>
                                        </Link>
                                    </td>
                                    <td v-if="idx === 0" :rowspan="order.products.length" class="border border-gray-400 px-4 py-2 text-center">
                                        <DangerButton @click="deleteOrder(order.id, order.customer.name)">
                                            <i class="fa-solid fa-trash"></i>
                                        </DangerButton>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                    <div class="p-4 flex justify-center">
                        <nav class="inline-flex -space-x-px" aria-label="Pagination">
                            <template v-for="(link, idx) in links" :key="idx">
                                <Link v-if="link.url" :href="link.url" class="px-4 py-2 border bg-white text-gray-700 text-sm hover:bg-gray-100" v-html="link.label" />
                                <span v-else class="px-4 py-2 border bg-gray-100 text-sm text-gray-500" v-html="link.label"></span>
                            </template>
                        </nav>
                    </div>

                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
